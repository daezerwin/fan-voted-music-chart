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
        return Cache::remember('charts:daily:latest', now()->addHour(), fn () => Chart::query()
            ->where('chart_type', ChartType::Daily)
            ->latest('chart_date')
            ->first());
    }
}
