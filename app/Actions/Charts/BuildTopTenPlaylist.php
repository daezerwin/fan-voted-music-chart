<?php

namespace App\Actions\Charts;

use App\Enums\ChartType;
use App\Models\Chart;
use App\Models\ChartEntry;
use Illuminate\Support\Collection;

class BuildTopTenPlaylist
{
    /**
     * Build the current Top 10 queue from the latest daily chart, skipping
     * any song (or its artist) that has since been deactivated.
     *
     * @return Collection<int, ChartEntry>
     */
    public function __invoke(): Collection
    {
        $chart = Chart::query()
            ->where('chart_type', ChartType::Daily)
            ->latest('chart_date')
            ->first();

        if ($chart === null) {
            return collect();
        }

        return $chart->entries()
            ->orderBy('rank')
            ->limit(10)
            ->with('song.artist')
            ->get()
            ->filter(fn ($entry) => $entry->song->is_active && $entry->song->artist->is_active)
            ->values();
    }
}
