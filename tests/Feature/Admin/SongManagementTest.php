<?php

namespace Tests\Feature\Admin;

use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SongManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_ordinary_user_cannot_manage_songs(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.songs.index'))->assertForbidden();
    }

    public function test_admin_can_create_a_song_from_a_youtube_watch_url(): void
    {
        $admin = User::factory()->admin()->create();
        $artist = Artist::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.songs.store'), [
            'artist_id' => $artist->id,
            'genre_id' => $genre->id,
            'title' => 'New Single',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ&list=abc',
            'is_active' => '1',
            'voting_enabled' => '1',
        ]);

        $song = Song::query()->where('title', 'New Single')->firstOrFail();

        $response->assertRedirect(route('admin.songs.edit', $song));
        $this->assertSame('dQw4w9WgXcQ', $song->youtube_video_id);
        $this->assertSame('new-single', $song->slug);
    }

    public function test_invalid_youtube_url_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $artist = Artist::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.songs.store'), [
            'artist_id' => $artist->id,
            'genre_id' => $genre->id,
            'title' => 'New Single',
            'youtube_url' => 'not a url at all',
        ]);

        $response->assertSessionHasErrors('youtube_url');
        $this->assertSame(0, Song::query()->count());
    }

    public function test_duplicate_youtube_video_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        Song::factory()->create(['youtube_video_id' => 'dQw4w9WgXcQ']);
        $artist = Artist::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.songs.store'), [
            'artist_id' => $artist->id,
            'genre_id' => $genre->id,
            'title' => 'Different Title',
            'youtube_url' => 'https://youtu.be/dQw4w9WgXcQ',
        ]);

        $response->assertSessionHasErrors('youtube_url');
        $this->assertSame(1, Song::query()->where('youtube_video_id', 'dQw4w9WgXcQ')->count());
    }

    public function test_admin_can_update_a_song_keeping_its_own_video_id(): void
    {
        $admin = User::factory()->admin()->create();
        $song = Song::factory()->create(['youtube_video_id' => 'dQw4w9WgXcQ']);

        $response = $this->actingAs($admin)->put(route('admin.songs.update', $song), [
            'artist_id' => $song->artist_id,
            'genre_id' => $song->genre_id,
            'title' => 'Renamed Title',
            'youtube_url' => 'https://youtu.be/dQw4w9WgXcQ',
        ]);

        $response->assertRedirect(route('admin.songs.edit', $song));
        $song->refresh();
        $this->assertSame('Renamed Title', $song->title);
        $this->assertSame('dQw4w9WgXcQ', $song->youtube_video_id);
    }
}
