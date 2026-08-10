<?php

namespace App\Actions\Discovery;

use App\Actions\Charts\GetLatestDailyChart;
use App\Models\ChartEntry;
use Illuminate\Support\Collection;

class GetBiggestGainers
{
    public function __construct(private readonly GetLatestDailyChart $latestDailyChart) {}

    /**
     * Entries from the latest daily chart with the largest positive movement.
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
            ->where('movement', '>', 0)
            ->when($genreId !== null, fn ($entries) => $entries->whereHas('song', fn ($songs) => $songs->where('genre_id', $genreId)))
            ->orderByDesc('movement')
            ->limit($limit)
            ->with('song.artist')
            ->get();
    }
}
