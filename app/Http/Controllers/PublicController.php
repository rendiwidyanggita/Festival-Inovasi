<?php

namespace App\Http\Controllers;

use App\Models\Festival;
use App\Models\AwardCategory;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * Landing page - show active/latest festival info.
     */
    public function index(): View
    {
        $festivals = Festival::with(['festivalCategories.awardCategories', 'settings'])
            ->orderBy('year', 'desc')
            ->get();

        $activeFestival = $festivals->firstWhere('status.value', 'ongoing') 
            ?? $festivals->firstWhere('status.value', 'preparation') 
            ?? $festivals->first();

        $stats = [
            'total_innovations' => $activeFestival ? \App\Models\Innovation::where('festival_id', $activeFestival->id)->count() : 0,
            'total_institutions' => $activeFestival ? \App\Models\Institution::where('festival_id', $activeFestival->id)->count() : 0,
            'total_subcategories' => $activeFestival ? \App\Models\AwardCategory::where('festival_id', $activeFestival->id)->count() : 0,
            'total_juries' => $activeFestival ? \App\Models\FestivalJury::where('festival_id', $activeFestival->id)->where('status', 'active')->count() : 0,
        ];

        return view('public.index', compact('festivals', 'activeFestival', 'stats'));
    }

    /**
     * Show festival detail with categories.
     */
    public function festival(Festival $festival): View
    {
        $festival->load(['festivalCategories.awardCategories' => function ($query) {
            $query->orderBy('sort_order');
        }]);

        return view('public.festival', compact('festival'));
    }

    /**
     * Show award category with participants and results.
     */
    public function category(Festival $festival, AwardCategory $awardCategory): View
    {
        $awardCategory->load(['festivalCategory']);

        $isPublished = $festival->hasPublishedResults();

        if ($awardCategory->festivalCategory->assessment_type->value === 'innovation') {
            $participants = $awardCategory->innovations()
                ->with('institution')
                ->orderBy('name')
                ->get();

            if ($isPublished) {
                // Load winners if results are published
                $awardCategory->load(['innovationWinners.innovation.institution']);
            }
        } else {
            $participants = $awardCategory->institutions()
                ->orderBy('name')
                ->get();

            if ($isPublished) {
                // Load winners if results are published
                $awardCategory->load(['institutionWinners.institution']);
            }
        }

        return view('public.category', compact('festival', 'awardCategory', 'participants', 'isPublished'));
    }
}
