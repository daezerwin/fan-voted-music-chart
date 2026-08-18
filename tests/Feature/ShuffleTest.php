<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShuffleTest extends TestCase
{
    use RefreshDatabase;

    public function test_shuffle_all_redirects_to_an_active_song(): void
    {
        $song = Song::factory()->create();

        $response = $this->get(route('shuffle.all'));

        $response->assertRedirect(route('songs.show', $song));
    }

    public function test_shuffle_all_ignores_inactive_songs(): void
    {
        Song::factory()->inactive()->create();

        $response = $this->get(route('shuffle.all'));

        $response->assertNotFound();
    }

    public function test_artist_shuffle_only_picks_that_artists_songs(): void
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);
        Song::factory()->create();

        $response = $this->get(route('artists.shuffle', $artist));

        $response->assertRedirect(route('songs.show', $song));
    }

    public function test_artist_shuffle_is_not_found_for_an_inactive_artist(): void
    {
        $artist = Artist::factory()->inactive()->create();

        $response = $this->get(route('artists.shuffle', $artist));

        $response->assertNotFound();
    }

    public function test_genre_shuffle_only_picks_that_genres_songs(): void
    {
        $genre = Genre::factory()->create();
        $song = Song::factory()->create(['genre_id' => $genre->id]);
        Song::factory()->create();

        $response = $this->get(route('genres.shuffle', $genre));

        $response->assertRedirect(route('songs.show', $song));
    }
}
