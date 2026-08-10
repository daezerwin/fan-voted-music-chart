<?php

namespace Database\Factories;

use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Song>
 */
class SongFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(3, true));

        return [
            'artist_id' => Artist::factory(),
            'genre_id' => Genre::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'youtube_video_id' => fake()->unique()->regexify('[A-Za-z0-9_-]{11}'),
            'release_date' => fake()->dateTimeBetween('-3 years')->format('Y-m-d'),
            'cover_image' => null,
            'description' => fake()->paragraph(),
            'is_active' => true,
            'voting_enabled' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function votingDisabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'voting_enabled' => false,
        ]);
    }
}
