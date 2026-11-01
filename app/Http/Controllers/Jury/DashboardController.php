<?php

namespace App\Http\Controllers\Jury;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the jury dashboard.
     */
    public function __invoke(): View
    {
        $user = auth()->user();
        
        // Get festivals where this jury is participating
        $festivals = Festival::whereHas('festivalJuries', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('status', 'active');
        })
        ->with(['festivalJuries' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])
        ->orderBy('year', 'desc')
        ->get();
        
        // Get subcategories assigned to this jury with progress
        $participations = [];
        foreach ($festivals as $festival) {
            $festivalJury = $festival->festivalJuries->first();
            if ($festivalJury) {
                $subcategories = $festivalJury->participations()
                    ->with(['awardCategory' => function ($query) {
                        $query->withCount([
                            'institutions',
                            'innovations',
                        ]);
                    }])
                    ->get();
                
                foreach ($subcategories as $participation) {
                    $awardCategory = $participation->awardCategory;
                    $totalEntries = $awardCategory->assessment_type->value === 'institution'
                        ? $awardCategory->institutions_count
                        : $awardCategory->innovations_count;
                    
                    // Count completed assessments (simplified - will be implemented in Phase 3)
                    $completedCount = 0;
                    
                    $participations[] = [
                        'festival' => $festival,
                        'subcategory' => $awardCategory,
                        'participation' => $participation,
                        'total_entries' => $totalEntries,
                        'completed_count' => $completedCount,
                        'progress_percentage' => $totalEntries > 0 ? round(($completedCount / $totalEntries) * 100) : 0,
                    ];
                }
            }
        }
        
        return view('jury.dashboard', compact('festivals', 'participations'));
    }
}
