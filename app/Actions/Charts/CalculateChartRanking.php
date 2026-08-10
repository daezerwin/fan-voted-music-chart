<?php

namespace App\Actions\Charts;

use App\Enums\ChartType;
use App\Models\Chart;
use App\Models\Vote;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class CalculateChartRanking
{
    /**
     * Rank songs by votes cast on the given date. All ranking/tie-break logic
     * for the chart engine is centralized here — never duplicate this in a
     * controller, view, or ad-hoc query elsewhere.
     *
     * Primary sort: vote count, descending. Ties break, in order, by:
     * 1. Earlier time the final vote total was reached (last vote timestamp).
     * 2. Better previous chart rank.
     * 3. Stable song ID, for full determinism.
     *
     * @return Collection<int, array{song_id: int, vote_count: int, rank: int, previous_rank: int|null}>
     */
    public function __invoke(CarbonInterface $date, ChartType $chartType): Collection
    {
        $tallies = Vote::query()
            ->selectRaw('song_id, COUNT(*) as vote_count, MAX(created_at) as last_vote_at')
            ->where('vote_date', $date->toDateString())
            ->whereHas('song', fn ($songs) => $songs
                ->where('is_active', true)
                ->whereHas('artist', fn ($artists) => $artists->where('is_active', true)))
            ->groupBy('song_id')
            ->get();

        $previousRanks = $this->previousRanks($chartType, $date);

        return $tallies
            ->map(fn ($tally) => [
                'song_id' => $tally->song_id,
                'vote_count' => (int) $tally->vote_count,
                'last_vote_at' => $tally->last_vote_at,
                'previous_rank' => $previousRanks[$tally->song_id] ?? null,
            ])
            ->sort(fn ($a, $b) => $b['vote_count'] <=> $a['vote_count']
                ?: $a['last_vote_at'] <=> $b['last_vote_at']
                ?: ($a['previous_rank'] ?? PHP_INT_MAX) <=> ($b['previous_rank'] ?? PHP_INT_MAX)
                ?: $a['song_id'] <=> $b['song_id'])
            ->values()
            ->map(function (array $entry, int $index) {
                $entry['rank'] = $index + 1;

                return $entry;
            });
    }

    /**
     * @return array<int, int>
     */
    private function previousRanks(ChartType $chartType, CarbonInterface $date): array
    {
        $previousChart = Chart::query()
            ->where('chart_type', $chartType)
            ->where('chart_date', $date->copy()->subDay()->toDateString())
            ->first();

        if ($previousChart === null) {
            return [];
        }

        return $previousChart->entries()->pluck('rank', 'song_id')->all();
    }
}
