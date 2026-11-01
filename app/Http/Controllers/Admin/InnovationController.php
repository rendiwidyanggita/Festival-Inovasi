<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Innovation\StoreRequest;
use App\Http\Requests\Innovation\UpdateRequest;
use App\Models\Innovation;
use App\Models\AwardCategory;
use App\Models\Institution;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InnovationController extends Controller
{
    public function index(): View
    {
        $festivalId = session('selected_festival_id');

        $innovations = Innovation::with(['awardCategory.festivalCategory', 'institution'])
            ->where('festival_id', $festivalId)
            ->when(request('award_category_id'), function ($query) {
                $query->where('award_category_id', request('award_category_id'));
            })
            ->orderBy('name')
            ->paginate(15);

        return view('admin.innovations.index', compact('innovations'));
    }

    public function create(): View
    {
        $festivalId = session('selected_festival_id');

        $awardCategories = AwardCategory::where('festival_id', $festivalId)
            ->whereHas('festivalCategory', function ($query) {
                $query->where('assessment_type', 'innovation');
            })
            ->orderBy('name')
            ->get();

        $institutions = Institution::where('festival_id', $festivalId)
            ->orderBy('name')
            ->get();

        return view('admin.innovations.create', compact('awardCategories', 'institutions'));
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $festivalId = session('selected_festival_id');

        $awardCategory = AwardCategory::find($request->award_category_id);

        Innovation::create([
            'festival_id' => $festivalId,
            'award_category_id' => $request->award_category_id,
            'institution_id' => $request->institution_id,
            'name' => $request->name,
            'innovator_name' => $request->innovator_name,
        ]);

        session()->flash('success', 'Inovasi berhasil dibuat.');

        return redirect()->route('admin.innovations.index');
    }

    public function edit(Innovation $innovation): View
    {
        $festivalId = session('selected_festival_id');

        $awardCategories = AwardCategory::where('festival_id', $festivalId)
            ->whereHas('festivalCategory', function ($query) {
                $query->where('assessment_type', 'innovation');
            })
            ->orderBy('name')
            ->get();

        $institutions = Institution::where('festival_id', $festivalId)
            ->orderBy('name')
            ->get();

        return view('admin.innovations.edit', compact('innovation', 'awardCategories', 'institutions'));
    }

    public function update(UpdateRequest $request, Innovation $innovation): RedirectResponse
    {
        $isRosterLocked = $innovation->awardCategory->isRosterLocked();
        $oldValues = [
            'name' => $innovation->name,
            'innovator_name' => $innovation->innovator_name,
        ];

        // Update innovation
        $updateData = [
            'name' => $request->name,
            'innovator_name' => $request->innovator_name,
        ];

        if (!$isRosterLocked) {
            $updateData['award_category_id'] = $request->award_category_id ?? $innovation->award_category_id;
            $updateData['institution_id'] = $request->institution_id ?? $innovation->institution_id;
        }

        $innovation->update($updateData);

        // Log audit trail if roster is locked (scoring already started)
        if ($isRosterLocked && ($oldValues['name'] !== $request->name || $oldValues['innovator_name'] !== $request->innovator_name)) {
            AuditService::logChange(
                auth()->user(),
                $innovation,
                'update_identity',
                $oldValues,
                [
                    'name' => $request->name,
                    'innovator_name' => $request->innovator_name,
                ]
            );
        }

        session()->flash('success', 'Inovasi berhasil diperbarui.');

        return redirect()->route('admin.innovations.index');
    }

    public function destroy(Innovation $innovation): RedirectResponse
    {
        // Use policy to check authorization (will throw 403 if not allowed)
        Gate::authorize('delete', $innovation);

        $innovation->delete();

        session()->flash('success', 'Inovasi berhasil dihapus.');

        return redirect()->route('admin.innovations.index');
    }
}
