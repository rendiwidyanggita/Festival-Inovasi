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
            $query->where('user_id', $user->id)
                  ->with(['participations.awardCategory.festivalCategory']);
        }])
        ->orderBy('year', 'desc')
        ->get();

        foreach ($festivals as $festival) {
            $festivalJury = $festival->festivalJuries->first();
            $participations = $festivalJury ? $festivalJury->participations : collect();

            foreach ($participations as $participation) {
                $awardCategory = $participation->awardCategory;
                $isInnovation = $awardCategory->festivalCategory->assessment_type->value === 'innovation';

                $total = $isInnovation 
                    ? $awardCategory->innovations()->count() 
                    : $awardCategory->institutions()->count();

                $assessed = $isInnovation
                    ? $participation->innovationAssessments()->whereIn('status', ['submitted', 'locked'])->count()
                    : $participation->institutionAssessments()->whereIn('status', ['submitted', 'locked'])->count();

                $percentage = $total > 0 ? round(($assessed / $total) * 100) : 0;

                $participation->progress = [
                    'assessed' => $assessed,
                    'total' => $total,
                    'percentage' => $percentage,
                ];
            }

            $festival->juryParticipations = $participations;
        }

        return view('jury.dashboard', compact('festivals'));
    }
}
