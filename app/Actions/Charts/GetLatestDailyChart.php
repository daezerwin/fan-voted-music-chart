<?php

namespace App\Actions\Charts;

use App\Enums\ChartType;
use App\Models\Chart;
use Illuminate\Support\Facades\Cache;

class GetLatestDailyChart
{
    /**
     * The "which chart is current" lookup is hit on nearly every page
     * (homepage, chart page, all three discovery sections) but only
     * changes once a day, so it's cached — and explicitly invalidated by
     * GenerateDailyChart, never left to expire stale.
     */
    public function __invoke(): ?Chart
    {
        $query = fn () => Chart::query()
            ->where('chart_type', ChartType::Daily)
            ->latest('chart_date')
            ->first();

        $chart = Cache::remember('charts:daily:latest', now()->addHour(), $query);

        // A cached entry can outlive the class shape it was serialized
        // under (e.g. a Redis value written before a deploy), in which case
        // unserialize() hands back a __PHP_Incomplete_Class instead of a
        // Chart. Treat that the same as a cache miss rather than crashing.
        if ($chart !== null && ! $chart instanceof Chart) {
            Cache::forget('charts:daily:latest');

            return Cache::remember('charts:daily:latest', now()->addHour(), $query);
        }

        return $chart;
    }
}
