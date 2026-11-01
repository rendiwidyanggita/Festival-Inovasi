<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RevisionRequest;
use App\Models\AwardCategory;
use App\Services\ScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScoringLifecycleController extends Controller
{
    public function __construct(
        protected ScoringService $scoringService
    ) {}

    /**
     * Open scoring for an award category.
     */
    public function openScoring(AwardCategory $awardCategory)
    {
        try {
            $this->scoringService->openScoring($awardCategory, Auth::user());

            return back()->with('success', "Penilaian untuk {$awardCategory->name} berhasil dibuka.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Close scoring for an award category.
     */
    public function closeScoring(AwardCategory $awardCategory)
    {
        try {
            $this->scoringService->closeScoring($awardCategory, Auth::user());

            return back()->with('success', "Penilaian untuk {$awardCategory->name} berhasil ditutup.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Lock an assessment.
     */
    public function lockAssessment(string $type, int $id)
    {
        try {
            $this->scoringService->lockAssessment($type, $id, Auth::user());

            return back()->with('success', 'Penilaian berhasil dikunci.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Request revision for an assessment.
     */
    public function requestRevision(RevisionRequest $request, string $type, int $id)
    {
        try {
            $this->scoringService->requestRevision(
                $type,
                $id,
                $request->reason,
                Auth::user()
            );

            return back()->with('success', 'Permintaan revisi berhasil dikirim.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
