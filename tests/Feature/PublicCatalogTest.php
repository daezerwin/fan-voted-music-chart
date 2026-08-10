<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_lists_recent_songs_and_artists(): void
    {
        Song::factory()->create();

        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_artist_index_lists_active_artists_only(): void
    {
        $active = Artist::factory()->create();
        $inactive = Artist::factory()->inactive()->create();

        $response = $this->get('/artists');

        $response->assertOk();
        $response->assertSee($active->name);
        $response->assertDontSee($inactive->name);
    }

    public function test_inactive_artist_page_is_not_found(): void
    {
        $artist = Artist::factory()->inactive()->create();

        $response = $this->get(route('artists.show', $artist));

        $response->assertNotFound();
    }

    public function test_active_artist_page_lists_its_active_songs(): void
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);
        $hiddenSong = Song::factory()->inactive()->create(['artist_id' => $artist->id]);

        $response = $this->get(route('artists.show', $artist));

        $response->assertOk();
        $response->assertSee($song->title);
        $response->assertDontSee($hiddenSong->title);
    }

    public function test_song_page_shows_song_and_artist(): void
    {
        $song = Song::factory()->create();

        $response = $this->get(route('songs.show', $song));

        $response->assertOk();
        $response->assertSee($song->title);
        $response->assertSee($song->artist->name);
    }

    public function test_inactive_song_page_is_not_found(): void
    {
        $song = Song::factory()->inactive()->create();

        $response = $this->get(route('songs.show', $song));

        $response->assertNotFound();
    }

    public function test_genre_page_lists_songs_in_that_genre(): void
    {
        $genre = Genre::factory()->create();
        $song = Song::factory()->create(['genre_id' => $genre->id]);

        $response = $this->get(route('genres.show', $genre));

        $response->assertOk();
        $response->assertSee($song->title);
    }

    public function test_search_finds_matching_artists_and_songs(): void
    {
        $artist = Artist::factory()->create(['name' => 'Zzyzx Collective']);
        $song = Song::factory()->create(['title' => 'Zzyzx Anthem']);

        $response = $this->get('/search?q=Zzyzx');

        $response->assertOk();
        $response->assertSee($artist->name);
        $response->assertSee($song->title);
    }

    public function test_search_without_a_query_shows_no_results(): void
    {
        $response = $this->get('/search');

        $response->assertOk();
        $response->assertSee('Start typing to search');
    }
}
