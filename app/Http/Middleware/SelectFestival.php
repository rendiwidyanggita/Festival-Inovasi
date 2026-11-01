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
        
        if ($festivalId) {
            $festival = \App\Models\Festival::find($festivalId);
            
            if ($festival) {
                View::share('currentFestival', $festival);
                session(['currentFestival' => $festival]);
            }
        }
        
        // If no festival selected and accessing admin routes that need festival context
        if (! $festivalId && $request->routeIs('admin.*') && ! $request->routeIs('admin.select-festival*') && ! $request->routeIs('admin.festivals.*')) {
            return redirect()->route('admin.select-festival.index')
                ->with('warning', 'Silakan pilih periode festival terlebih dahulu.');
        }
        
        return $next($request);
    }
}
