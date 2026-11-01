<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Festival\StoreRequest;
use App\Http\Requests\Festival\UpdateRequest;
use App\Models\Festival;
use App\Models\FestivalSetting;
use Illuminate\Http\RedirectResponse;
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

    public function store(StoreRequest $request): RedirectResponse
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

        session()->flash('success', 'Festival berhasil dibuat.');

        return redirect()->route('admin.festivals.index');
    }

    public function edit(Festival $festival): View
    {
        $festival->load('settings');

        return view('admin.festivals.edit', compact('festival'));
    }

    public function update(UpdateRequest $request, Festival $festival): RedirectResponse
    {
        $festival->update([
            'name' => $request->name,
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

        session()->flash('success', 'Festival berhasil diperbarui.');

        return redirect()->route('admin.festivals.index');
    }
}
