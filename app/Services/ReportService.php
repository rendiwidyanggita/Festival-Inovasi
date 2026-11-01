<?php

namespace App\Services;

use App\Models\AwardCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ReportService
{
    public function __construct(
        protected RankingService $rankingService
    ) {}

    /**
     * Generate PDF report for an award category.
     */
    public function generatePDF(AwardCategory $category): Response
    {
        $rankings = $this->rankingService->getRankings($category);
        $festival = $category->festival;
        $festivalCategory = $category->festivalCategory;

        $isInnovation = $festivalCategory->assessment_type->value === 'innovation';
        
        $winners = $isInnovation
            ? $category->innovationWinners()->with(['innovation.institution'])->get()
            : $category->institutionWinners()->with(['institution'])->get();

        $pdf = Pdf::loadView('reports.pdf', [
            'category' => $category,
            'awardCategory' => $category,
            'festival' => $festival,
            'festivalCategory' => $festivalCategory,
            'rankings' => $rankings,
            'winners' => $winners,
            'isInnovation' => $isInnovation,
            'printDate' => now()->format('d/m/Y H:i'),
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = "laporan-{$category->name}-{$festival->year}.pdf";

        return $pdf->download($filename);
    }
}
