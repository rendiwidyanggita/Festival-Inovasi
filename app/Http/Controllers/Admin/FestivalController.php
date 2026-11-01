<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Festival\StoreRequest;
use App\Http\Requests\Festival\UpdateRequest;
use App\Models\Festival;
use App\Models\FestivalSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
class FestivalController extends Controller
{
    public function index(): View
    {
        $festivals = Festival::with('settings')
            ->orderBy('year', 'desc')
            ->paginate(15);

        return view('admin.festivals.index', compact('festivals'));
    }

    public function create(): View
    {
        return view('admin.festivals.create');
    }

    public function store(StoreRequest $request): RedirectResponse|JsonResponse
    {
        $festival = Festival::create([
            'year' => $request->year,
            'name' => $request->name,
        ]);

        // Create default settings
        FestivalSetting::create([
            'festival_id' => $festival->id,
            'organizer_name' => $request->organizer_name ?? 'Panitia Festival',
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Festival berhasil dibuat.',
                'festival' => $festival->load('settings'),
            ]);
        }

        session()->flash('success', 'Festival berhasil dibuat.');

        return redirect()->route('admin.festivals.index');
    }

    public function edit(Festival $festival): View
    {
        $festival->load('settings');

        return view('admin.festivals.edit', compact('festival'));
    }

    public function update(UpdateRequest $request, Festival $festival): RedirectResponse|JsonResponse
    {
        $festival->update([
            'name' => $request->name,
            'status' => $request->status ?? $festival->status,
            'year' => $request->year ?? $festival->year,
        ]);

        // Update or create settings
        if ($festival->settings) {
            $festival->settings->update([
                'organizer_name' => $request->organizer_name ?? $festival->settings->organizer_name,
                'organizer_logo_path' => $request->organizer_logo_path,
                'report_title' => $request->report_title,
                'report_footer' => $request->report_footer,
            ]);
        } else {
            FestivalSetting::create([
                'festival_id' => $festival->id,
                'organizer_name' => $request->organizer_name ?? 'Panitia Festival',
                'organizer_logo_path' => $request->organizer_logo_path,
                'report_title' => $request->report_title,
                'report_footer' => $request->report_footer,
            ]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Festival berhasil diperbarui.',
                'festival' => $festival->load('settings'),
            ]);
        }

        session()->flash('success', 'Festival berhasil diperbarui.');

        return redirect()->route('admin.festivals.index');
    }

    public function destroy(Festival $festival): RedirectResponse|JsonResponse
    {
        Gate::authorize('delete', $festival);

        if ($festival->festivalCategories()->exists() || $festival->festivalJuries()->exists()) {
            $errorMessage = 'Festival tidak dapat dihapus karena memiliki data kategori atau juri terkait.';
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 422);
            }

            session()->flash('error', $errorMessage);
            return redirect()->route('admin.festivals.index');
        }

        $festival->settings()?->delete();
        $festival->delete();

        if (session('selected_festival_id') == $festival->id) {
            session()->forget('selected_festival_id');
        }

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Festival berhasil dihapus.',
            ]);
        }

        session()->flash('success', 'Festival berhasil dihapus.');

        return redirect()->route('admin.festivals.index');
    }
}
