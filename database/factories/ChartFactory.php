<?php

namespace Database\Factories;

use App\Enums\ChartType;
use App\Models\Chart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Chart>
 */
class ChartFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chart_type' => ChartType::Daily,
            'chart_date' => now()->toDateString(),
            'generated_at' => now(),
        ];
    }
}
