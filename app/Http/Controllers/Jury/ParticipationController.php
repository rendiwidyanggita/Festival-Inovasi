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

        // Partisipasi yang sudah dimiliki juri pada festival ini
        $existingCategoryIds = JurySubcategoryParticipation::where('festival_jury_id', $festivalJury->id)
            ->where('festival_id', $festival->id)
            ->pluck('award_category_id');

        // Hanya kategori BARU (belum menjadi partisipasi) yang wajib preparation;
        // kategori yang sudah ada boleh dipertahankan meski sudah dibuka/ditutup.
        $newCategoryIds = collect($selectedIds)->reject(
            fn ($id) => $existingCategoryIds->contains((int) $id)
        );

        $notPreparation = AwardCategory::where('festival_id', $festival->id)
            ->whereIn('id', $newCategoryIds)
            ->where('scoring_status', '!=', ScoringStatus::Preparation->value)
            ->pluck('name');

        if ($notPreparation->isNotEmpty()) {
            return back()->withErrors([
                'award_categories' => 'Subkategori berikut sudah tidak dapat dipilih: ' . $notPreparation->implode(', ') . '.',
            ]);
        }

        // Roster juri terkunci setelah subkategori dibuka: participasi pada
        // subkategori non-preparation tidak boleh dilepas (protected dari FK restrict
        // dan dari hilangnya data penilaian yang sudah ada).
        $lockedParticipations = JurySubcategoryParticipation::with('awardCategory')
            ->where('festival_jury_id', $festivalJury->id)
            ->where('festival_id', $festival->id)
            ->whereNotIn('award_category_id', $selectedIds)
            ->whereHas('awardCategory', fn ($q) => $q->where('scoring_status', '!=', ScoringStatus::Preparation->value))
            ->get();

        if ($lockedParticipations->isNotEmpty()) {
            $names = $lockedParticipations->pluck('awardCategory.name')->implode(', ');

            return back()->withErrors([
                'award_categories' => "Subkategori {$names} sudah dibuka/ditutup sehingga penugasan tidak dapat dilepas.",
            ]);
        }

        try {
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
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withErrors([
                'award_categories' => 'Penugasan tidak dapat diperbarui karena sudah ada data penilaian.',
            ]);
        }

        return redirect()->route('jury.participations.index', $festival)
            ->with('success', 'Pilihan subkategori berhasil diperbarui.');
    }
}
