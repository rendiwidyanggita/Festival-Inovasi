<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Institution\StoreRequest;
use App\Http\Requests\Institution\UpdateRequest;
use App\Models\Institution;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstitutionController extends Controller
{
    public function index(): View
    {
        $festivalId = session('selected_festival_id');

        $institutions = Institution::with('awardCategory.festivalCategory')
            ->where('festival_id', $festivalId)
            ->when(request('award_category_id'), function ($query) {
                $query->where('award_category_id', request('award_category_id'));
            })
            ->orderBy('name')
            ->paginate(15);

        return view('admin.institutions.index', compact('institutions'));
    }

    public function create(): View
    {
        $festivalId = session('selected_festival_id');

        $awardCategories = \App\Models\AwardCategory::where('festival_id', $festivalId)
            ->whereHas('festivalCategory', function ($query) {
                $query->where('assessment_type', 'institution');
            })
            ->orderBy('name')
            ->get();

        return view('admin.institutions.create', compact('awardCategories'));
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $festivalId = session('selected_festival_id');

        Institution::create([
            'festival_id' => $festivalId,
            'award_category_id' => $request->award_category_id,
            'name' => $request->name,
        ]);

        session()->flash('success', 'Instansi berhasil dibuat.');

        return redirect()->route('admin.institutions.index');
    }

    public function edit(Institution $institution): View
    {
        $festivalId = session('selected_festival_id');

        $awardCategories = \App\Models\AwardCategory::where('festival_id', $festivalId)
            ->whereHas('festivalCategory', function ($query) {
                $query->where('assessment_type', 'institution');
            })
            ->orderBy('name')
            ->get();

        return view('admin.institutions.edit', compact('institution', 'awardCategories'));
    }

    public function update(UpdateRequest $request, Institution $institution): RedirectResponse
    {
        $isRosterLocked = $institution->awardCategory->isRosterLocked();
        $oldValues = [
            'name' => $institution->name,
        ];

        // Update institution
        $institution->update([
            'name' => $request->name,
            'award_category_id' => $isRosterLocked ? $institution->award_category_id : ($request->award_category_id ?? $institution->award_category_id),
        ]);

        // Log audit trail if roster is locked (scoring already started)
        if ($isRosterLocked && $oldValues['name'] !== $request->name) {
            AuditService::logChange(
                auth()->user(),
                $institution,
                'update_identity',
                $oldValues,
                ['name' => $request->name]
            );
        }

        session()->flash('success', 'Instansi berhasil diperbarui.');

        return redirect()->route('admin.institutions.index');
    }

    public function destroy(Institution $institution): RedirectResponse
    {
        // Check if scoring_status is not preparation
        if ($institution->awardCategory->isRosterLocked()) {
            session()->flash('error', 'Instansi tidak dapat dihapus karena penilaian sudah dimulai.');

            return redirect()->route('admin.institutions.index');
        }

        $institution->delete();

        session()->flash('success', 'Instansi berhasil dihapus.');

        return redirect()->route('admin.institutions.index');
    }
}
