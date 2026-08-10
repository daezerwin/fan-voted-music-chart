<?php

namespace App\Actions\Charts;

use App\Enums\ChartType;
use App\Models\Chart;
use App\Models\ChartEntry;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GenerateDailyChart
{
    public function __construct(private readonly CalculateChartRanking $ranking) {}

    /**
     * Generate (or regenerate) the daily chart snapshot for a given vote date.
     * Runs inside a transaction so a partially generated chart is never
     * exposed, and is safe to rerun for the same date: existing entries for
     * that chart are replaced rather than duplicated.
     *
     * Guarded by a distributed lock keyed on the date, not just called from
     * the scheduled command: this action is also invoked directly by the
     * admin's manual "regenerate" tool, which a scheduler-level
     * withoutOverlapping() guard would never see. Concurrent callers for the
     * same date queue up and run in turn rather than racing each other.
     */
    public function __invoke(CarbonInterface $date): Chart
    {
        return Cache::lock('charts:generate:daily:'.$date->toDateString(), 300)
            ->block(3, function () use ($date) {
                return DB::transaction(function () use ($date) {
                    $chart = Chart::query()->updateOrCreate(
                        ['chart_type' => ChartType::Daily, 'chart_date' => $date->toDateString()],
                        ['generated_at' => now()],
                    );

                    $chart->entries()->delete();

                    $ranked = ($this->ranking)($date, ChartType::Daily);

                    if ($ranked->isNotEmpty()) {
                        $history = $this->historicalStats($ranked->pluck('song_id')->all());

                        ChartEntry::query()->insert(
                            $ranked->map(fn (array $entry) => $this->buildRow($chart->id, $entry, $history))->all()
                        );
                    }

                    Cache::forget('charts:daily:latest');

                    return $chart;
                });
            });
    }

    /**
     * @return array<string, mixed>
     */
    private function buildRow(int $chartId, array $entry, array $history): array
    {
        $songHistory = $history[$entry['song_id']] ?? null;

        return [
            'chart_id' => $chartId,
            'song_id' => $entry['song_id'],
            'rank' => $entry['rank'],
            'vote_count' => $entry['vote_count'],
            'previous_rank' => $entry['previous_rank'],
            'movement' => $entry['previous_rank'] !== null ? $entry['previous_rank'] - $entry['rank'] : null,
            'peak_rank' => $songHistory !== null ? min($songHistory->best_rank, $entry['rank']) : $entry['rank'],
            'charting_periods' => ($songHistory->periods ?? 0) + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * @param  list<int>  $songIds
     * @return array<int, ChartEntry>
     */
    private function historicalStats(array $songIds): array
    {
        return ChartEntry::query()
            ->selectRaw('song_id, MIN(`rank`) as best_rank, COUNT(*) as periods')
            ->whereIn('song_id', $songIds)
            ->groupBy('song_id')
            ->get()
            ->keyBy('song_id')
            ->all();
    }
}
