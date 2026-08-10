<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Database\Seeder;

class SongSeeder extends Seeder
{
    public function run(): void
    {
        $artistIds = Artist::query()->where('is_active', true)->pluck('id');
        $genreIds = Genre::query()->pluck('id');

        Song::factory()
            ->count(60)
            ->sequence(fn () => [
                'artist_id' => $artistIds->random(),
                'genre_id' => $genreIds->random(),
            ])
            ->create();

        Song::factory()->inactive()->create([
            'artist_id' => $artistIds->random(),
            'genre_id' => $genreIds->random(),
        ]);

        Song::factory()->votingDisabled()->create([
            'artist_id' => $artistIds->random(),
            'genre_id' => $genreIds->random(),
        ]);
    }
}
