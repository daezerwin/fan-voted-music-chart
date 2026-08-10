<?php

namespace Tests\Feature\Charts;

use App\Actions\Charts\GenerateDailyChart;
use App\Enums\ChartType;
use App\Models\Chart;
use App\Models\ChartEntry;
use App\Models\Song;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ChartGenerationTest extends TestCase
{
    use RefreshDatabase;

    private function voteAt(Song $song, string $date, string $createdAt): void
    {
        $vote = Vote::factory()->create([
            'user_id' => User::factory(),
            'song_id' => $song->id,
            'vote_date' => $date,
        ]);

        $vote->forceFill(['created_at' => $createdAt])->saveQuietly();
    }

    public function test_songs_are_ranked_by_vote_count_descending(): void
    {
        $date = '2026-08-10';
        $winner = Song::factory()->create();
        $loser = Song::factory()->create();

        $this->voteAt($winner, $date, "{$date} 10:00:00");
        $this->voteAt($winner, $date, "{$date} 10:01:00");
        $this->voteAt($loser, $date, "{$date} 10:00:00");

        $chart = app(GenerateDailyChart::class)(Carbon::parse($date));

        $entries = $chart->entries()->orderBy('rank')->get();

        $this->assertSame($winner->id, $entries[0]->song_id);
        $this->assertSame(2, $entries[0]->vote_count);
        $this->assertSame($loser->id, $entries[1]->song_id);
        $this->assertSame(1, $entries[1]->vote_count);
    }

    public function test_ties_break_by_earlier_final_vote_time(): void
    {
        $date = '2026-08-10';
        $earlier = Song::factory()->create();
        $later = Song::factory()->create();

        $this->voteAt($earlier, $date, "{$date} 09:00:00");
        $this->voteAt($later, $date, "{$date} 15:00:00");

        $chart = app(GenerateDailyChart::class)(Carbon::parse($date));

        $entries = $chart->entries()->orderBy('rank')->get();

        $this->assertSame($earlier->id, $entries[0]->song_id);
        $this->assertSame($later->id, $entries[1]->song_id);
    }

    public function test_new_song_is_a_new_entry_with_no_previous_rank(): void
    {
        $date = '2026-08-10';
        $song = Song::factory()->create();
        $this->voteAt($song, $date, "{$date} 09:00:00");

        $chart = app(GenerateDailyChart::class)(Carbon::parse($date));
        $entry = $chart->entries()->first();

        $this->assertNull($entry->previous_rank);
        $this->assertNull($entry->movement);
        $this->assertSame(1, $entry->charting_periods);
        $this->assertSame(1, $entry->peak_rank);
        $this->assertTrue($entry->isNewEntry());
        $this->assertFalse($entry->isReentry());
    }

    public function test_movement_and_peak_rank_are_tracked_across_consecutive_charts(): void
    {
        $day1 = '2026-08-10';
        $day2 = '2026-08-11';

        $riser = Song::factory()->create();
        $faller = Song::factory()->create();

        // Day 1: riser is #2 (1 vote), faller is #1 (2 votes).
        $this->voteAt($faller, $day1, "{$day1} 09:00:00");
        $this->voteAt($faller, $day1, "{$day1} 09:01:00");
        $this->voteAt($riser, $day1, "{$day1} 09:00:00");

        app(GenerateDailyChart::class)(Carbon::parse($day1));

        // Day 2: riser overtakes with 3 votes, faller gets none.
        $this->voteAt($riser, $day2, "{$day2} 09:00:00");
        $this->voteAt($riser, $day2, "{$day2} 09:01:00");
        $this->voteAt($riser, $day2, "{$day2} 09:02:00");

        $chart2 = app(GenerateDailyChart::class)(Carbon::parse($day2));
        $riserEntry = $chart2->entries()->where('song_id', $riser->id)->firstOrFail();

        $this->assertSame(1, $riserEntry->rank);
        $this->assertSame(2, $riserEntry->previous_rank);
        $this->assertSame(1, $riserEntry->movement);
        $this->assertSame(1, $riserEntry->peak_rank);
        $this->assertSame(2, $riserEntry->charting_periods);
    }

    public function test_reentry_is_detected_when_a_song_charted_before_but_not_yesterday(): void
    {
        $day1 = '2026-08-01';
        $day3 = '2026-08-03';

        $song = Song::factory()->create();
        $this->voteAt($song, $day1, "{$day1} 09:00:00");
        app(GenerateDailyChart::class)(Carbon::parse($day1));

        // No chart generated for day2 — the song has no entry the day before day3.
        $this->voteAt($song, $day3, "{$day3} 09:00:00");
        $chart3 = app(GenerateDailyChart::class)(Carbon::parse($day3));

        $entry = $chart3->entries()->where('song_id', $song->id)->firstOrFail();

        $this->assertNull($entry->previous_rank);
        $this->assertSame(2, $entry->charting_periods);
        $this->assertFalse($entry->isNewEntry());
        $this->assertTrue($entry->isReentry());
    }

    public function test_generation_is_idempotent_when_rerun_for_the_same_date(): void
    {
        $date = '2026-08-10';
        $song = Song::factory()->create();
        $this->voteAt($song, $date, "{$date} 09:00:00");

        $first = app(GenerateDailyChart::class)(Carbon::parse($date));
        $second = app(GenerateDailyChart::class)(Carbon::parse($date));

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, Chart::query()->count());
        $this->assertSame(1, ChartEntry::query()->count());
    }

    public function test_inactive_songs_and_artists_are_excluded_from_ranking(): void
    {
        $date = '2026-08-10';
        $inactiveSong = Song::factory()->inactive()->create();
        $inactiveArtistSong = Song::factory()->create();
        $inactiveArtistSong->artist()->update(['is_active' => false]);

        $this->voteAt($inactiveSong, $date, "{$date} 09:00:00");
        $this->voteAt($inactiveArtistSong, $date, "{$date} 09:00:00");

        $chart = app(GenerateDailyChart::class)(Carbon::parse($date));

        $this->assertSame(0, $chart->entries()->count());
    }

    public function test_artisan_command_generates_a_chart_for_the_given_date(): void
    {
        $date = '2026-08-10';
        $song = Song::factory()->create();
        $this->voteAt($song, $date, "{$date} 09:00:00");

        $this->artisan('charts:generate-daily', ['date' => $date])
            ->assertExitCode(0);

        $this->assertTrue(
            Chart::query()
                ->where('chart_type', ChartType::Daily)
                ->whereDate('chart_date', $date)
                ->exists()
        );
    }
}
