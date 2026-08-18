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

        $response->assertRedirect(route('songs.show', [$song, 'shuffle' => 'all']));
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

        $response->assertRedirect(route('songs.show', [$song, 'shuffle' => 'artist', 'scope' => $artist->slug]));
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

        $response->assertRedirect(route('songs.show', [$song, 'shuffle' => 'genre', 'scope' => $genre->slug]));
    }

    public function test_song_page_shows_an_up_next_preview_and_link(): void
    {
        $song = Song::factory()->create();
        $nextSong = Song::factory()->create();

        $response = $this->get(route('songs.show', [$song, 'shuffle' => 'all']));

        $response->assertOk();
        $response->assertSee('shuffle-youtube-player');
        $response->assertSee($nextSong->title);
        $response->assertSee(route('songs.show', [$nextSong, 'shuffle' => 'all']), false);
    }

    public function test_song_page_defaults_to_a_site_wide_next_pick(): void
    {
        $song = Song::factory()->create();
        $nextSong = Song::factory()->create();

        $response = $this->get(route('songs.show', $song));

        $response->assertOk();
        $response->assertSee($nextSong->title);
    }

    public function test_song_page_falls_back_to_a_site_wide_pick_for_an_unknown_scope(): void
    {
        $song = Song::factory()->create();
        $nextSong = Song::factory()->create();

        $response = $this->get(route('songs.show', [$song, 'shuffle' => 'artist', 'scope' => 'does-not-exist']));

        $response->assertOk();
        $response->assertSee($nextSong->title);
    }

    public function test_song_page_shows_no_up_next_when_it_is_the_only_song(): void
    {
        $song = Song::factory()->create();

        $response = $this->get(route('songs.show', $song));

        $response->assertOk();
        $response->assertSee('No other songs to play next yet.');
    }

    public function test_artist_scoped_next_pick_stays_within_that_artist(): void
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);
        $sameArtistSong = Song::factory()->create(['artist_id' => $artist->id]);
        $otherArtistSong = Song::factory()->create();

        $response = $this->get(route('songs.show', [$song, 'shuffle' => 'artist', 'scope' => $artist->slug]));

        $response->assertOk();
        $response->assertSee($sameArtistSong->title);
        $response->assertDontSee($otherArtistSong->title);
    }
}
