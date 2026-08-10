<?php

namespace Tests\Feature\Charts;

use App\Actions\Charts\BuildTopTenPlaylist;
use App\Models\Chart;
use App\Models\ChartEntry;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopTenPlayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_play_page_shows_empty_state_without_a_chart(): void
    {
        $response = $this->get(route('play'));

        $response->assertOk();
        $response->assertSee('No chart is available to play yet.');
    }

    public function test_play_page_lists_songs_from_the_latest_chart_in_rank_order(): void
    {
        $chart = Chart::factory()->create(['chart_date' => now()->toDateString()]);
        $first = Song::factory()->create(['title' => 'First Place']);
        $second = Song::factory()->create(['title' => 'Second Place']);

        ChartEntry::factory()->create(['chart_id' => $chart->id, 'song_id' => $first->id, 'rank' => 1]);
        ChartEntry::factory()->create(['chart_id' => $chart->id, 'song_id' => $second->id, 'rank' => 2]);

        $response = $this->get(route('play'));

        $response->assertOk();
        $response->assertSeeInOrder(['First Place', 'Second Place']);
        $response->assertSee($first->youtube_video_id);
    }

    public function test_playlist_excludes_songs_from_inactive_artists(): void
    {
        $chart = Chart::factory()->create(['chart_date' => now()->toDateString()]);

        $activeSong = Song::factory()->create();
        $inactiveArtistSong = Song::factory()->create();
        $inactiveArtistSong->artist()->update(['is_active' => false]);

        ChartEntry::factory()->create(['chart_id' => $chart->id, 'song_id' => $activeSong->id, 'rank' => 1]);
        ChartEntry::factory()->create(['chart_id' => $chart->id, 'song_id' => $inactiveArtistSong->id, 'rank' => 2]);

        $queue = (new BuildTopTenPlaylist)();

        $this->assertCount(1, $queue);
        $this->assertSame($activeSong->id, $queue->first()->song_id);
    }
}
