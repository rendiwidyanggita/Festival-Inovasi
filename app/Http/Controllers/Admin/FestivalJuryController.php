<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FestivalJury\StoreRequest;
use App\Models\FestivalJury;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FestivalJuryController extends Controller
{
    public function index(): View
    {
        $festivalId = session('selected_festival_id');

        $festivalJuries = FestivalJury::with('user')
            ->where('festival_id', $festivalId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.festival-juries.index', compact('festivalJuries'));
    }

    public function create(): View
    {
        $festivalId = session('selected_festival_id');

        // Get juries not yet assigned to this festival
        $assignedJuryIds = FestivalJury::where('festival_id', $festivalId)
            ->pluck('user_id');

        $availableJuries = User::where('role', UserRole::Jury->value)
            ->where('is_active', true)
            ->whereNotIn('id', $assignedJuryIds)
            ->orderBy('name')
            ->get();

        return view('admin.festival-juries.create', compact('availableJuries'));
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $festivalId = session('selected_festival_id');

        FestivalJury::create([
            'festival_id' => $festivalId,
            'user_id' => $request->user_id,
            'status' => 'active',
        ]);

        session()->flash('success', 'Juri berhasil ditambahkan ke festival.');

        return redirect()->route('admin.festival-juries.index');
    }

    public function update(FestivalJury $festival_jury): RedirectResponse
    {
        $newStatus = $festival_jury->status->value === 'active' ? 'inactive' : 'active';

        // Check if trying to deactivate and has incomplete participations
        if ($newStatus === 'inactive') {
            $hasIncompleteParticipations = $festival_jury->participations()
                ->where('status', '!=', 'completed')
                ->exists();

            if ($hasIncompleteParticipations) {
                session()->flash('warning', 'Juri masih memiliki penilaian yang belum selesai. Status tetap diubah.');

                // Still allow the update but warn
                $festival_jury->update(['status' => $newStatus]);

                return redirect()->route('admin.festival-juries.index');
            }
        }

        $festival_jury->update(['status' => $newStatus]);

        session()->flash('success', 'Status juri berhasil diperbarui.');

        return redirect()->route('admin.festival-juries.index');
    }

    public function destroy(FestivalJury $festival_jury): RedirectResponse
    {
        // Check if has participations
        if ($festival_jury->participations()->exists()) {
            session()->flash('error', 'Juri tidak dapat dihapus karena sudah memiliki penugasan subkategori.');

            return redirect()->route('admin.festival-juries.index');
        }

        $festival_jury->delete();

        session()->flash('success', 'Juri berhasil dihapus dari festival.');

        return redirect()->route('admin.festival-juries.index');
    }
}
