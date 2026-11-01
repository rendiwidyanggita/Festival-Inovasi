<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AwardCategory;
use App\Models\Festival;
use App\Services\RankingService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct(
        protected RankingService $rankingService,
        protected ReportService $reportService
    ) {}

    /**
     * Display a listing of reports.
     */
    public function index(Request $request): View
    {
        $festival = $request->session()->get('selected_festival');

        if (!$festival) {
            return view('admin.reports.index', [
                'festival' => null,
                'awardCategories' => collect(),
            ]);
        }

        $festival = Festival::with(['awardCategories.festivalCategory'])->findOrFail($festival->id);

        $awardCategories = $festival->awardCategories()
            ->with(['festivalCategory', 'innovationWinners', 'institutionWinners'])
            ->orderBy('sort_order')
            ->get();

        return view('admin.reports.index', compact('festival', 'awardCategories'));
    }

    /**
     * Preview report for an award category.
     */
    public function preview(AwardCategory $awardCategory): View
    {
        $rankings = $this->rankingService->getRankings($awardCategory);
        
        $isInnovation = $awardCategory->festivalCategory->assessment_type->value === 'innovation';
        
        $winners = $isInnovation
            ? $awardCategory->innovationWinners()->with(['innovation.institution'])->get()
            : $awardCategory->institutionWinners()->with(['institution'])->get();

        return view('admin.reports.preview', [
            'category' => $awardCategory,
            'festival' => $awardCategory->festival,
            'festivalCategory' => $awardCategory->festivalCategory,
            'rankings' => $rankings,
            'winners' => $winners,
            'isInnovation' => $isInnovation,
        ]);
    }

    /**
     * Generate PDF report for an award category.
     */
    public function pdf(AwardCategory $awardCategory): Response
    {
        return $this->reportService->generatePDF($awardCategory);
    }
}
