<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AwardCategory;
use App\Models\Festival;
use App\Models\Institution;
use App\Models\Innovation;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function __invoke(): View
    {
        $currentFestival = session('currentFestival');
        
        $stats = [];
        
        if ($currentFestival) {
            $stats = [
                'festivals_count' => Festival::count(),
                'categories_count' => $currentFestival->festivalCategories()->count(),
                'subcategories_count' => AwardCategory::where('festival_id', $currentFestival->id)->count(),
                'institutions_count' => Institution::where('festival_id', $currentFestival->id)->count(),
                'innovations_count' => Innovation::where('festival_id', $currentFestival->id)->count(),
                'active_juries_count' => User::where('role', 'jury')
                    ->where('is_active', true)
                    ->whereHas('festivalJuries', function ($query) use ($currentFestival) {
                        $query->where('festival_id', $currentFestival->id)
                              ->where('status', 'active');
                    })
                    ->count(),
            ];
        }
        
        return view('admin.dashboard', compact('currentFestival', 'stats'));
    }
}
