<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignWinnersRequest;
use App\Models\AwardCategory;
use App\Models\Festival;
use App\Models\InnovationWinner;
use App\Models\InstitutionWinner;
use App\Services\RankingService;
use App\Services\WinnerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WinnerController extends Controller
{
    public function __construct(
        protected RankingService $rankingService,
        protected WinnerService $winnerService
    ) {}

    /**
     * Display a listing of winners.
     */
    public function index(Request $request): View
    {
        $festivalId = $request->session()->get('selected_festival_id') 
            ?? $request->session()->get('selected_festival')?->id
            ?? $request->session()->get('currentFestival')?->id;

        $festival = $festivalId ? Festival::with(['awardCategories.festivalCategory'])->find($festivalId) : null;

        if (!$festival) {
            $festival = Festival::with(['awardCategories.festivalCategory'])->orderBy('year', 'desc')->first();
            if ($festival) {
                session(['selected_festival_id' => $festival->id, 'currentFestival' => $festival]);
            }
        }

        if (!$festival) {
            return view('admin.winners.index', [
                'winners' => collect(),
                'festival' => null,
                'awardCategories' => collect(),
            ]);
        }

        $awardCategories = $festival->awardCategories()
            ->with(['festivalCategory', 'innovationWinners.innovation.institution', 'institutionWinners.institution'])
            ->orderBy('sort_order')
            ->get();

        return view('admin.winners.index', compact('festival', 'awardCategories'));
    }

    /**
     * Show rankings for an award category.
     */
    public function rankings(AwardCategory $awardCategory): View
    {
        $rankings = $this->rankingService->getRankings($awardCategory);
        
        $isInnovation = $awardCategory->festivalCategory->assessment_type->value === 'innovation';
        
        $existingWinners = $isInnovation
            ? $awardCategory->innovationWinners()->with(['innovation.institution'])->get()->keyBy('innovation_id')
            : $awardCategory->institutionWinners()->with(['institution'])->get()->keyBy('institution_id');

        return view('admin.winners.rankings', compact('awardCategory', 'rankings', 'existingWinners', 'isInnovation'));
    }

    /**
     * Store newly assigned winners.
     */
    public function store(AssignWinnersRequest $request, AwardCategory $awardCategory): RedirectResponse
    {
        try {
            $this->winnerService->assignWinners(
                $awardCategory,
                $request->validated()['winners'],
                $request->user()
            );

            return redirect()
                ->route('admin.award-categories.rankings', $awardCategory)
                ->with('success', 'Pemenang berhasil ditetapkan.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update an existing winner.
     */
    public function update(Request $request, string $winnerType, int $winnerId): RedirectResponse
    {
        $request->validate([
            'position' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $winnerModel = $winnerType === 'innovation' ? InnovationWinner::class : InstitutionWinner::class;
        $winner = $winnerModel::findOrFail($winnerId);

        try {
            $winner->update([
                'position' => $request->position,
                'notes' => $request->notes,
            ]);

            AuditService::logChange(
                $request->user(),
                $winner,
                'update_winner',
                $winner->getOriginal(),
                $winner->fresh()->toArray()
            );

            return back()->with('success', 'Data pemenang berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove a winner.
     */
    public function destroy(string $winnerType, int $winnerId): RedirectResponse
    {
        $winnerModel = $winnerType === 'innovation' ? InnovationWinner::class : InstitutionWinner::class;
        $winner = $winnerModel::findOrFail($winnerId);

        try {
            $this->winnerService->removeWinner($winner, request()->user());

            return back()->with('success', 'Pemenang berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
