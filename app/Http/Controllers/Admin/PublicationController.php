<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
    /**
     * Publish results for a festival.
     */
    public function publish(Request $request, Festival $festival): RedirectResponse
    {
        if ($festival->hasPublishedResults()) {
            return back()->withErrors(['error' => 'Hasil sudah dipublikasikan sebelumnya.']);
        }

        // Publikasi hanya bila ada pemenang yang ditetapkan
        $hasWinners = $festival->awardCategories()
            ->where(function ($query) {
                $query->whereHas('innovationWinners')
                    ->orWhereHas('institutionWinners');
            })
            ->exists();

        if (! $hasWinners) {
            return back()->withErrors(['error' => 'Belum ada pemenang yang ditetapkan. Tetapkan pemenang terlebih dahulu sebelum mempublikasikan hasil.']);
        }

        $festival->update([
            'results_published_at' => now(),
            'results_published_by' => $request->user()->id,
        ]);

        AuditService::logChange(
            $request->user(),
            $festival,
            'publish_results',
            ['results_published_at' => null],
            ['results_published_at' => $festival->results_published_at->toIso8601String()]
        );

        return back()->with('success', 'Hasil berhasil dipublikasikan ke publik.');
    }

    /**
     * Unpublish results for a festival.
     */
    public function unpublish(Request $request, Festival $festival): RedirectResponse
    {
        if (!$festival->hasPublishedResults()) {
            return back()->withErrors(['error' => 'Hasil belum dipublikasikan.']);
        }

        $oldTimestamp = $festival->results_published_at->toIso8601String();

        $festival->update([
            'results_published_at' => null,
            'results_published_by' => null,
        ]);

        AuditService::logChange(
            $request->user(),
            $festival,
            'unpublish_results',
            ['results_published_at' => $oldTimestamp],
            ['results_published_at' => null]
        );

        return back()->with('success', 'Publikasi hasil berhasil dibatalkan.');
    }
}
