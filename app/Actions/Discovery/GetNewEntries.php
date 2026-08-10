<?php

namespace App\Actions\Discovery;

use App\Enums\ChartType;
use App\Models\Chart;
use App\Models\ChartEntry;
use Illuminate\Support\Collection;

class GetNewEntries
{
    /**
     * Entries from the latest daily chart that weren't on the previous
     * chart — covers both brand-new entries and re-entries.
     *
     * @return Collection<int, ChartEntry>
     */
    public function __invoke(?int $genreId = null, int $limit = 5): Collection
    {
        $chart = Chart::query()->where('chart_type', ChartType::Daily)->latest('chart_date')->first();

        if ($chart === null) {
            return collect();
        }

        return $chart->entries()
            ->whereNull('previous_rank')
            ->when($genreId !== null, fn ($entries) => $entries->whereHas('song', fn ($songs) => $songs->where('genre_id', $genreId)))
            ->orderBy('rank')
            ->limit($limit)
            ->with('song.artist')
            ->get();
    }
}
