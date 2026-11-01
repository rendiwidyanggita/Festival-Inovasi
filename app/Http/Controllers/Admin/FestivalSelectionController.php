<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FestivalSelectionController extends Controller
{
    /**
     * Display festival selection page.
     */
    public function index(): View
    {
        $festivals = Festival::orderBy('year', 'desc')->get();
        
        return view('admin.select-festival.index', compact('festivals'));
    }
    
    /**
     * Select a festival for the session.
     */
    public function select(Festival $festival): RedirectResponse
    {
        session(['selected_festival_id' => $festival->id]);
        
        return redirect()->route('admin.dashboard')
            ->with('success', "Festival {$festival->name} ({$festival->year}) dipilih.");
    }
}
