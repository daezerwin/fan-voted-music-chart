<?php

namespace Tests\Feature\Admin;

use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_admin_page_renders_for_an_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $artist = Artist::factory()->create();
        $genre = Genre::factory()->create();
        $song = Song::factory()->create();

        $pages = [
            route('admin.dashboard'),
            route('admin.artists.index'),
            route('admin.artists.create'),
            route('admin.artists.edit', $artist),
            route('admin.songs.index'),
            route('admin.songs.create'),
            route('admin.songs.edit', $song),
            route('admin.genres.index'),
            route('admin.genres.create'),
            route('admin.genres.edit', $genre),
            route('admin.votes.index'),
            route('admin.charts.index'),
        ];

        foreach ($pages as $page) {
            $this->actingAs($admin)->get($page)->assertOk();
        }
    }
}
