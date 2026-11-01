<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AwardCategory\StoreRequest;
use App\Http\Requests\AwardCategory\UpdateRequest;
use App\Models\AwardCategory;
use App\Models\FestivalCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AwardCategoryController extends Controller
{
    public function index(FestivalCategory $festival_category): View
    {
        $awardCategories = AwardCategory::where('festival_category_id', $festival_category->id)
            ->orderBy('sort_order')
            ->paginate(15);

        return view('admin.award-categories.index', compact('festival_category', 'awardCategories'));
    }

    public function create(FestivalCategory $festival_category): View
    {
        return view('admin.award-categories.create', compact('festival_category'));
    }

    public function store(StoreRequest $request, FestivalCategory $festival_category): RedirectResponse
    {
        $festivalId = session('selected_festival_id');

        AwardCategory::create([
            'festival_id' => $festivalId,
            'festival_category_id' => $festival_category->id,
            'name' => $request->name,
            'winner_count' => $request->winner_count,
            'scoring_status' => 'preparation',
            'sort_order' => $request->sort_order ?? 0,
        ]);

        session()->flash('success', 'Subkategori berhasil dibuat.');

        return redirect()->route('admin.festival-categories.award-categories.index', $festival_category);
    }

    public function edit(AwardCategory $award_category): View
    {
        $award_category->load('festivalCategory');

        return view('admin.award-categories.edit', compact('award_category'));
    }

    public function update(UpdateRequest $request, AwardCategory $award_category): RedirectResponse
    {
        $award_category->update([
            'name' => $request->name,
            'winner_count' => $request->winner_count,
            'sort_order' => $request->sort_order ?? $award_category->sort_order,
        ]);

        session()->flash('success', 'Subkategori berhasil diperbarui.');

        return redirect()->route('admin.festival-categories.award-categories.index', $award_category->festival_category_id);
    }

    public function destroy(AwardCategory $award_category): RedirectResponse
    {
        // Check if scoring_status is not preparation
        if ($award_category->scoring_status->value !== 'preparation') {
            session()->flash('error', 'Subkategori tidak dapat dihapus karena sudah dalam proses penilaian.');

            return redirect()->route('admin.festival-categories.award-categories.index', $award_category->festival_category_id);
        }

        // Check if has institutions/innovations/participations
        if ($award_category->institutions()->exists() || $award_category->innovations()->exists() || $award_category->participations()->exists()) {
            session()->flash('error', 'Subkategori tidak dapat dihapus karena masih memiliki data terkait.');

            return redirect()->route('admin.festival-categories.award-categories.index', $award_category->festival_category_id);
        }

        $festivalCategoryId = $award_category->festival_category_id;
        $award_category->delete();

        session()->flash('success', 'Subkategori berhasil dihapus.');

        return redirect()->route('admin.festival-categories.award-categories.index', $festivalCategoryId);
    }
}
