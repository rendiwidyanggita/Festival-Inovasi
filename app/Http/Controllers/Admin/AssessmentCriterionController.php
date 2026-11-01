<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssessmentCriterion\StoreRequest;
use App\Http\Requests\AssessmentCriterion\UpdateRequest;
use App\Models\AssessmentCriterion;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssessmentCriterionController extends Controller
{
    public function index(): View
    {
        $festivalId = session('selected_festival_id');

        $criteria = AssessmentCriterion::where('festival_id', $festivalId)
            ->orderBy('assessment_type')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('assessment_type');

        return view('admin.assessment-criteria.index', compact('criteria'));
    }

    public function create(): View
    {
        return view('admin.assessment-criteria.create');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $festivalId = session('selected_festival_id');

        AssessmentCriterion::create([
            'festival_id' => $festivalId,
            'assessment_type' => $request->assessment_type,
            'name' => $request->name,
            'weight' => $request->weight,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        session()->flash('success', 'Kriteria berhasil dibuat.');

        return redirect()->route('admin.assessment-criteria.index');
    }

    public function edit(AssessmentCriterion $criterion): View
    {
        return view('admin.assessment-criteria.edit', compact('criterion'));
    }

    public function update(UpdateRequest $request, AssessmentCriterion $criterion): RedirectResponse
    {
        $criterion->update([
            'name' => $request->name,
            'weight' => $request->weight,
            'sort_order' => $request->sort_order ?? $criterion->sort_order,
        ]);

        session()->flash('success', 'Kriteria berhasil diperbarui.');

        return redirect()->route('admin.assessment-criteria.index');
    }

    public function destroy(AssessmentCriterion $criterion): RedirectResponse
    {
        // Check if criterion has any assessment details
        $hasInnovationDetails = $criterion->innovationAssessmentDetails()->exists();
        $hasInstitutionDetails = $criterion->institutionAssessmentDetails()->exists();

        if ($hasInnovationDetails || $hasInstitutionDetails) {
            session()->flash('error', 'Kriteria tidak dapat dihapus karena sudah digunakan dalam penilaian.');

            return redirect()->route('admin.assessment-criteria.index');
        }

        $criterion->delete();

        session()->flash('success', 'Kriteria berhasil dihapus.');

        return redirect()->route('admin.assessment-criteria.index');
    }
}
