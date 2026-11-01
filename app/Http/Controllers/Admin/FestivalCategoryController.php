<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FestivalCategory\StoreRequest;
use App\Http\Requests\FestivalCategory\UpdateRequest;
use App\Models\FestivalCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;


class FestivalCategoryController extends Controller
{
    public function index(): View
    {
        $festivalId = session('selected_festival_id');

        $categories = FestivalCategory::with('awardCategories')
            ->where('festival_id', $festivalId)
            ->orderBy('sort_order')
            ->paginate(15);

        return view('admin.festival-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.festival-categories.create');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $festivalId = session('selected_festival_id');

        FestivalCategory::create([
            'festival_id' => $festivalId,
            'name' => $request->name,
            'assessment_type' => $request->assessment_type,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        session()->flash('success', 'Kategori berhasil dibuat.');

        return redirect()->route('admin.festival-categories.index');
    }

    public function edit(FestivalCategory $festival_category): View
    {
        $category = $festival_category;
        return view('admin.festival-categories.edit', compact('festival_category', 'category'));
    }

    public function update(UpdateRequest $request, FestivalCategory $festival_category): RedirectResponse
    {
        $festival_category->update([
            'name' => $request->name,
            'sort_order' => $request->sort_order ?? $festival_category->sort_order,
        ]);

        session()->flash('success', 'Kategori berhasil diperbarui.');

        return redirect()->route('admin.festival-categories.index');
    }

    public function destroy(FestivalCategory $festival_category): RedirectResponse
    {
        // Use policy to check authorization (will throw 403 if not allowed)
        Gate::authorize('delete', $festival_category);

        $festival_category->delete();

        session()->flash('success', 'Kategori berhasil dihapus.');

        return redirect()->route('admin.festival-categories.index');
    }
}
