<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_lists_active_artists_and_songs(): void
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create();
        $inactiveArtist = Artist::factory()->inactive()->create();

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee(route('artists.show', $artist), false);
        $response->assertSee(route('songs.show', $song), false);
        $response->assertDontSee(route('artists.show', $inactiveArtist), false);
    }

    public function test_robots_txt_references_the_sitemap(): void
    {
        // robots.txt is a static file nginx serves directly — it never
        // reaches Laravel's router in production, so it can't be exercised
        // through the HTTP test client. Check the file contents instead.
        $contents = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Sitemap: /sitemap.xml', $contents);
    }
}
