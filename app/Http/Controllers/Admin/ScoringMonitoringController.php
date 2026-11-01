<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AssessmentStatus;
use App\Enums\AssessmentType;
use App\Http\Controllers\Controller;
use App\Models\AwardCategory;
use App\Models\Festival;
use App\Models\Innovation;
use App\Models\Institution;
use App\Models\JurySubcategoryParticipation;
use Illuminate\Http\Request;

class ScoringMonitoringController extends Controller
{
    /**
     * Display scoring monitoring matrix for a festival.
     */
    public function index(Request $request)
    {
        $festivals = Festival::orderBy('year', 'desc')->get();

        $selectedFestival = $request->get('festival') ?? session('selected_festival_id') ?? $festivals->first()?->id;

        if (!$selectedFestival) {
            return redirect()->route('admin.select-festival.index')
                ->withErrors(['error' => 'Pilih festival terlebih dahulu.']);
        }

        $festival = Festival::findOrFail($selectedFestival);

        $subcategories = AwardCategory::with('festivalCategory')
            ->where('festival_id', $selectedFestival)
            ->orderBy('sort_order')
            ->get();

        $selectedSubcategory = $request->get('subcategory');

        // If subcategory not explicitly selected, we can optionally pick the first or keep null
        if (!$selectedSubcategory && $request->has('subcategory')) {
            $selectedSubcategory = null;
        } elseif (!$selectedSubcategory && $subcategories->isNotEmpty()) {
            $selectedSubcategory = $subcategories->first()->id;
        }

        $activeSubcategory = $selectedSubcategory ? $subcategories->find($selectedSubcategory) : null;

        $objects = collect();
        $juries = collect();
        $matrix = [];
        $assessmentType = 'innovation';

        if ($activeSubcategory) {
            $isInnovation = $activeSubcategory->assessment_type === AssessmentType::Innovation;
            $assessmentType = $isInnovation ? 'innovation' : 'institution';

            $objects = $isInnovation
                ? Innovation::where('award_category_id', $activeSubcategory->id)->orderBy('name')->get()
                : Institution::where('award_category_id', $activeSubcategory->id)->orderBy('name')->get();

            // Participations for this award category
            $participations = JurySubcategoryParticipation::with('festivalJury.user')
                ->where('festival_id', $festival->id)
                ->where('award_category_id', $activeSubcategory->id)
                ->get();

            $juries = $participations->map(function ($participation) {
                return (object) [
                    'id' => $participation->id,
                    'user' => $participation->festivalJury->user,
                    'participation' => $participation,
                ];
            });

            // Build matrix: $matrix[$juryId][$objectId]
            foreach ($participations as $participation) {
                $assessments = $isInnovation
                    ? $participation->innovationAssessments
                    : $participation->institutionAssessments;

                foreach ($objects as $object) {
                    $assessment = $assessments->first(function ($a) use ($isInnovation, $object) {
                        return $isInnovation ? $a->innovation_id === $object->id : $a->institution_id === $object->id;
                    });

                    $matrix[$participation->id][$object->id] = [
                        'id' => $assessment?->id,
                        'status' => $assessment ? $assessment->status->value : 'not_started',
                        'score' => $assessment ? $assessment->weightedTotal() : null,
                    ];
                }
            }
        }

        return view('admin.scoring.monitoring', compact(
            'festivals',
            'festival',
            'selectedFestival',
            'subcategories',
            'selectedSubcategory',
            'activeSubcategory',
            'objects',
            'juries',
            'matrix',
            'assessmentType'
        ));
    }
}
