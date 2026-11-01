<?php

namespace App\Http\Controllers\Jury;

use App\Enums\AssessmentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Jury\SaveDraftRequest;
use App\Http\Requests\Jury\SubmitAssessmentRequest;
use App\Models\AwardCategory;
use App\Models\Innovation;
use App\Models\InnovationAssessment;
use App\Models\Institution;
use App\Models\InstitutionAssessment;
use App\Models\JurySubcategoryParticipation;
use App\Services\ScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    public function __construct(
        protected ScoringService $scoringService
    ) {}

    /**
     * Show list of objects to assess in an award category.
     */
    public function show(AwardCategory $awardCategory)
    {
        $user = Auth::user();

        $participation = JurySubcategoryParticipation::with(['festivalJury'])
            ->where('festival_id', $awardCategory->festival_id)
            ->where('award_category_id', $awardCategory->id)
            ->whereHas('festivalJury', fn ($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $isInnovation = $awardCategory->assessment_type === AssessmentType::Innovation;

        $objects = $isInnovation
            ? Innovation::where('award_category_id', $awardCategory->id)->orderBy('name')->get()
            : Institution::where('award_category_id', $awardCategory->id)->orderBy('name')->get();

        $objects->load(['assessments' => function ($query) use ($participation) {
            $query->where('jury_subcategory_participation_id', $participation->id);
        }]);

        foreach ($objects as $object) {
            $assessment = $object->assessments->first();
            $object->assessment_status = $assessment ? $assessment->status->value : 'not_started';
        }

        $totalObjects = $objects->count();
        $assessedCount = $objects->filter(fn ($o) => in_array($o->assessment_status, ['submitted', 'locked']))->count();
        $progressPercentage = $totalObjects > 0 ? round(($assessedCount / $totalObjects) * 100) : 0;
        $assessmentType = $isInnovation ? 'innovation' : 'institution';

        return view('jury.subcategories.show', compact(
            'awardCategory',
            'participation',
            'objects',
            'isInnovation',
            'assessmentType',
            'totalObjects',
            'assessedCount',
            'progressPercentage'
        ));
    }
    /**
     * Show assessment form for a specific object.
     */
    public function assess(AwardCategory $awardCategory, string $type, int $id)
    {
        $user = Auth::user();

        $participation = JurySubcategoryParticipation::with(['festivalJury'])
            ->where('festival_id', $awardCategory->festival_id)
            ->where('award_category_id', $awardCategory->id)
            ->whereHas('festivalJury', fn ($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        if (!in_array($type, ['innovation', 'institution'])) {
            abort(404, 'Tipe penilaian tidak valid.');
        }

        $isInnovation = $type === 'innovation';

        $target = $isInnovation
            ? Innovation::where('award_category_id', $awardCategory->id)->findOrFail($id)
            : Institution::where('award_category_id', $awardCategory->id)->findOrFail($id);

        $assessment = $isInnovation
            ? InnovationAssessment::with('details.criterion')
                ->where('jury_subcategory_participation_id', $participation->id)
                ->where('innovation_id', $target->id)
                ->first()
            : InstitutionAssessment::with('details.criterion')
                ->where('jury_subcategory_participation_id', $participation->id)
                ->where('institution_id', $target->id)
                ->first();

        $criteria = $awardCategory->festival->criteria()
            ->where('assessment_type', $awardCategory->assessment_type)
            ->orderBy('sort_order')
            ->get();

        // Map existing scores
        $existingScores = [];
        if ($assessment) {
            foreach ($assessment->details as $detail) {
                $existingScores[$detail->assessment_criterion_id] = $detail->score;
            }
        }

        $assessmentType = $type;
        $object = $target;

        return view('jury.assessments.form', compact(
            'awardCategory',
            'participation',
            'target',
            'object',
            'assessment',
            'criteria',
            'existingScores',
            'type',
            'assessmentType'
        ));
    }
    /**
     * Save assessment as draft.
     */
    public function draft(SaveDraftRequest $request, AwardCategory $awardCategory, string $type, int $id)
    {
        $user = Auth::user();

        $participation = JurySubcategoryParticipation::with(['festivalJury'])
            ->where('festival_id', $awardCategory->festival_id)
            ->where('award_category_id', $awardCategory->id)
            ->whereHas('festivalJury', fn ($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        if (!in_array($type, ['innovation', 'institution'])) {
            abort(404, 'Tipe penilaian tidak valid.');
        }

        $target = $type === 'innovation'
            ? Innovation::where('award_category_id', $awardCategory->id)->findOrFail($id)
            : Institution::where('award_category_id', $awardCategory->id)->findOrFail($id);

        try {
            $this->scoringService->saveDraft(
                $participation,
                $target,
                array_map(fn ($v) => is_numeric($v) ? (int) $v : $v, $request->scores ?? $request->criteria ?? []),
                $user
            );

            return redirect()->route('jury.subcategories.show', $awardCategory)
                ->with('success', 'Draf penilaian berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Submit assessment.
     */
    public function submit(SubmitAssessmentRequest $request, AwardCategory $awardCategory, string $type, int $id)
    {
        $user = Auth::user();

        $participation = JurySubcategoryParticipation::with(['festivalJury'])
            ->where('festival_id', $awardCategory->festival_id)
            ->where('award_category_id', $awardCategory->id)
            ->whereHas('festivalJury', fn ($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        if (!in_array($type, ['innovation', 'institution'])) {
            abort(404, 'Tipe penilaian tidak valid.');
        }

        $target = $type === 'innovation'
            ? Innovation::where('award_category_id', $awardCategory->id)->findOrFail($id)
            : Institution::where('award_category_id', $awardCategory->id)->findOrFail($id);

        try {
            $this->scoringService->submitAssessment(
                $participation,
                $target,
                array_map(fn ($v) => is_numeric($v) ? (int) $v : $v, $request->scores ?? $request->criteria ?? []),
                $user
            );

            return redirect()->route('jury.subcategories.show', $awardCategory)
                ->with('success', 'Penilaian berhasil dikirim.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
