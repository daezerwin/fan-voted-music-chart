<?php

namespace Tests\Feature\Charts;

use App\Actions\Charts\GenerateDailyChart;
use App\Actions\Charts\GetLatestDailyChart;
use App\Models\Chart;
use App\Models\Song;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class GetLatestDailyChartTest extends TestCase
{
    use RefreshDatabase;

    public function test_result_is_cached_across_calls(): void
    {
        $chart = Chart::factory()->create();

        $first = (new GetLatestDailyChart)();
        $newerChart = Chart::factory()->create(['chart_date' => now()->addDay()->toDateString()]);
        $second = (new GetLatestDailyChart)();

        $this->assertSame($chart->id, $first->id);
        $this->assertSame($chart->id, $second->id, 'Expected the cached result, not the newer chart.');
    }

    public function test_cache_is_invalidated_when_a_new_chart_is_generated(): void
    {
        Chart::factory()->create(['chart_date' => '2026-08-01']);
        $stale = (new GetLatestDailyChart)();
        $this->assertSame('2026-08-01', $stale->chart_date->toDateString());

        $song = Song::factory()->create();
        $vote = Vote::factory()->create(['song_id' => $song->id, 'vote_date' => '2026-08-10']);
        $vote->forceFill(['created_at' => '2026-08-10 09:00:00'])->saveQuietly();

        app(GenerateDailyChart::class)(Carbon::parse('2026-08-10'));

        $fresh = (new GetLatestDailyChart)();
        $this->assertSame('2026-08-10', $fresh->chart_date->toDateString());
    }

    public function test_returns_null_without_a_chart(): void
    {
        Cache::forget('charts:daily:latest');

        $this->assertNull((new GetLatestDailyChart)());
    }
}
