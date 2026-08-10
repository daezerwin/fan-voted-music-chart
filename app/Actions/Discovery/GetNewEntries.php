<?php

namespace App\Actions\Discovery;

use App\Actions\Charts\GetLatestDailyChart;
use App\Models\ChartEntry;
use Illuminate\Support\Collection;

class GetNewEntries
{
    public function __construct(private readonly GetLatestDailyChart $latestDailyChart) {}

    /**
     * Entries from the latest daily chart that weren't on the previous
     * chart — covers both brand-new entries and re-entries.
     *
     * @return Collection<int, ChartEntry>
     */
    public function __invoke(?int $genreId = null, int $limit = 5): Collection
    {
        $chart = ($this->latestDailyChart)();

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
