<?php

namespace Tests\Feature\Discovery;

use App\Actions\Discovery\GetBiggestGainers;
use App\Actions\Discovery\GetNewEntries;
use App\Actions\Discovery\GetTrendingSongs;
use App\Models\Chart;
use App\Models\ChartEntry;
use App\Models\Genre;
use App\Models\Song;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscoveryHighlightsTest extends TestCase
{
    use RefreshDatabase;

    public function test_trending_songs_are_ordered_by_todays_vote_count(): void
    {
        $hot = Song::factory()->create();
        $cool = Song::factory()->create();

        Vote::factory()->count(3)->create(['song_id' => $hot->id, 'vote_date' => now()->toDateString()]);
        Vote::factory()->count(1)->create(['song_id' => $cool->id, 'vote_date' => now()->toDateString()]);

        $trending = (new GetTrendingSongs)();

        $this->assertSame($hot->id, $trending->first()->id);
        $this->assertSame(3, $trending->first()->votes_today);
    }

    public function test_trending_songs_can_be_scoped_to_a_genre(): void
    {
        $genre = Genre::factory()->create();
        $inGenre = Song::factory()->create(['genre_id' => $genre->id]);
        $otherGenre = Song::factory()->create();

        Vote::factory()->create(['song_id' => $inGenre->id, 'vote_date' => now()->toDateString()]);
        Vote::factory()->create(['song_id' => $otherGenre->id, 'vote_date' => now()->toDateString()]);

        $trending = (new GetTrendingSongs)($genre->id);

        $this->assertCount(1, $trending);
        $this->assertSame($inGenre->id, $trending->first()->id);
    }

    public function test_biggest_gainers_only_include_positive_movement(): void
    {
        $chart = Chart::factory()->create();
        $riser = Song::factory()->create();
        $faller = Song::factory()->create();
        $steady = Song::factory()->create();

        ChartEntry::factory()->create(['chart_id' => $chart->id, 'song_id' => $riser->id, 'movement' => 5]);
        ChartEntry::factory()->create(['chart_id' => $chart->id, 'song_id' => $faller->id, 'movement' => -3]);
        ChartEntry::factory()->create(['chart_id' => $chart->id, 'song_id' => $steady->id, 'movement' => 0]);

        $gainers = (new GetBiggestGainers)();

        $this->assertCount(1, $gainers);
        $this->assertSame($riser->id, $gainers->first()->song_id);
    }

    public function test_new_entries_have_no_previous_rank(): void
    {
        $chart = Chart::factory()->create();
        $newSong = Song::factory()->create();
        $returningSong = Song::factory()->create();

        ChartEntry::factory()->create(['chart_id' => $chart->id, 'song_id' => $newSong->id, 'previous_rank' => null, 'rank' => 1]);
        ChartEntry::factory()->create(['chart_id' => $chart->id, 'song_id' => $returningSong->id, 'previous_rank' => 4, 'rank' => 2]);

        $entries = (new GetNewEntries)();

        $this->assertCount(1, $entries);
        $this->assertSame($newSong->id, $entries->first()->song_id);
    }

    public function test_homepage_renders_discovery_sections(): void
    {
        Vote::factory()->create(['vote_date' => now()->toDateString()]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Trending Now');
        $response->assertSee('Biggest Gainers');
        $response->assertSee('New Entries');
    }

    public function test_genre_page_renders_scoped_discovery_sections(): void
    {
        $genre = Genre::factory()->create();
        $song = Song::factory()->create(['genre_id' => $genre->id]);
        Vote::factory()->create(['song_id' => $song->id, 'vote_date' => now()->toDateString()]);

        $response = $this->get(route('genres.show', $genre));

        $response->assertOk();
        $response->assertSee('Trending Now');
    }
}
