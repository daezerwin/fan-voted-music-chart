<?php

namespace App\Actions\Discovery;

use App\Enums\ChartType;
use App\Models\Chart;
use App\Models\ChartEntry;
use Illuminate\Support\Collection;

class GetBiggestGainers
{
    /**
     * Entries from the latest daily chart with the largest positive movement.
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
            ->where('movement', '>', 0)
            ->when($genreId !== null, fn ($entries) => $entries->whereHas('song', fn ($songs) => $songs->where('genre_id', $genreId)))
            ->orderByDesc('movement')
            ->limit($limit)
            ->with('song.artist')
            ->get();
    }
}
