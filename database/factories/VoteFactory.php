<?php

namespace Database\Factories;

use App\Models\Song;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vote>
 */
class VoteFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'song_id' => Song::factory(),
            'vote_date' => now()->toDateString(),
        ];
    }
}
