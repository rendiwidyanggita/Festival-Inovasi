<?php

namespace App\Http\Controllers\Jury;

use App\Enums\ParticipationStatus;
use App\Enums\ScoringStatus;
use App\Http\Controllers\Controller;
use App\Models\AwardCategory;
use App\Models\Festival;
use App\Models\FestivalJury;
use App\Models\JurySubcategoryParticipation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParticipationController extends Controller
{
    /**
     * Display juri's selected subcategories for a festival.
     */
    public function index(Festival $festival)
    {
        $user = Auth::user();

        $festivalJury = FestivalJury::where('festival_id', $festival->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $participations = JurySubcategoryParticipation::with('awardCategory.festivalCategory')
            ->where('festival_jury_id', $festivalJury->id)
            ->where('festival_id', $festival->id)
            ->get();

        $availableCategories = AwardCategory::with('festivalCategory')
            ->where('festival_id', $festival->id)
            ->where('scoring_status', ScoringStatus::Preparation)
            ->whereNotIn('id', $participations->pluck('award_category_id'))
            ->get();

        $festivals = Festival::where('id', $festival->id)->get();
        $selectedSubcategories = $participations->pluck('award_category_id');

        return view('jury.participations.index', compact(
            'festival',
            'festivals',
            'festivalJury',
            'participations',
            'availableCategories',
            'selectedSubcategories'
        ));
    }

    /**
     * Store/sync juri's selected subcategories.
     */
    public function store(Request $request, Festival $festival)
    {
        $user = Auth::user();

        $festivalJury = FestivalJury::where('festival_id', $festival->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $selectedIds = $request->input('subcategories') ?? $request->input('award_categories') ?? [];

        $request->merge([
            'award_categories' => $selectedIds,
        ]);

        $request->validate([
            'award_categories' => ['required', 'array'],
            'award_categories.*' => ['exists:award_categories,id'],
        ]);

        // Verify all selected categories are in preparation status
        $categories = AwardCategory::where('festival_id', $festival->id)
            ->whereIn('id', $selectedIds)
            ->get();

        $notPreparation = $categories->filter(
            fn ($c) => $c->scoring_status !== ScoringStatus::Preparation
        );

        if ($notPreparation->isNotEmpty()) {
            return back()->withErrors([
                'award_categories' => 'Beberapa subkategori sudah tidak dapat dipilih.',
            ]);
        }

        DB::transaction(function () use ($festival, $festivalJury, $selectedIds) {
            // Remove participations not in the new selection
            JurySubcategoryParticipation::where('festival_jury_id', $festivalJury->id)
                ->where('festival_id', $festival->id)
                ->whereNotIn('award_category_id', $selectedIds)
                ->delete();

            // Create new participations
            foreach ($selectedIds as $categoryId) {
                JurySubcategoryParticipation::firstOrCreate(
                    [
                        'festival_jury_id' => $festivalJury->id,
                        'festival_id' => $festival->id,
                        'award_category_id' => $categoryId,
                    ],
                    [
                        'status' => ParticipationStatus::Selected,
                    ]
                );
            }
        });

        return redirect()->route('jury.participations.index', $festival)
            ->with('success', 'Pilihan subkategori berhasil diperbarui.');
    }
}
