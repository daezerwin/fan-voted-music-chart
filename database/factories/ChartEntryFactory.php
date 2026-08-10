<?php

namespace Database\Factories;

use App\Models\Chart;
use App\Models\ChartEntry;
use App\Models\Song;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChartEntry>
 */
class ChartEntryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rank = fake()->numberBetween(1, 100);

        return [
            'chart_id' => Chart::factory(),
            'song_id' => Song::factory(),
            'rank' => $rank,
            'vote_count' => fake()->numberBetween(1, 1000),
            'previous_rank' => null,
            'movement' => null,
            'peak_rank' => $rank,
            'charting_periods' => 1,
        ];
    }
}
