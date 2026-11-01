<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SelectFestival
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $festivalId = session('selected_festival_id');
        $festival = null;
        
        if ($festivalId) {
            $festival = \App\Models\Festival::find($festivalId);
        }

        // Auto-select latest active/ongoing or latest festival if none selected
        if (!$festival) {
            $festival = \App\Models\Festival::where('status', \App\Enums\FestivalStatus::Ongoing)
                ->orderBy('year', 'desc')
                ->first()
                ?? \App\Models\Festival::orderBy('year', 'desc')->first();

            if ($festival) {
                $festivalId = $festival->id;
                session(['selected_festival_id' => $festival->id]);
            }
        }
        
        if ($festival) {
            session(['currentFestival' => $festival]);
            View::share('currentFestival', $festival);
        } else {
            View::share('currentFestival', null);
        }
        
        // If no festival exists in DB and accessing routes that need a festival
        if (! $festival && $request->routeIs('admin.*') && ! $request->routeIs('admin.select-festival*') && ! $request->routeIs('admin.festivals.*') && ! $request->routeIs('admin.dashboard')) {
            return redirect()->route('admin.select-festival.index')
                ->with('warning', 'Silakan buat atau pilih periode festival terlebih dahulu.');
        }
        
        return $next($request);
    }
}
