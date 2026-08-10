<?php

namespace Tests\Feature;

use App\Actions\Voting\CastVote;
use App\Enums\VoteResult;
use App\Models\Song;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VotingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_vote(): void
    {
        $song = Song::factory()->create();

        $response = $this->post(route('votes.store', $song));

        $response->assertRedirect(route('login'));
        $this->assertSame(0, Vote::query()->count());
    }

    public function test_authenticated_user_can_vote(): void
    {
        $user = User::factory()->create();
        $song = Song::factory()->create();

        $response = $this->actingAs($user)->post(route('votes.store', $song));

        $response->assertRedirect();
        $response->assertSessionHas('status');
        $this->assertTrue(
            Vote::query()
                ->where('user_id', $user->id)
                ->where('song_id', $song->id)
                ->whereDate('vote_date', now())
                ->exists()
        );
    }

    public function test_user_cannot_vote_for_the_same_song_twice_on_the_same_day(): void
    {
        $user = User::factory()->create();
        $song = Song::factory()->create();

        $this->actingAs($user)->post(route('votes.store', $song));
        $response = $this->actingAs($user)->post(route('votes.store', $song));

        $response->assertSessionHas('status', 'You have already voted for this song today.');
        $this->assertSame(1, Vote::query()->count());
    }

    public function test_user_may_vote_again_on_a_later_day(): void
    {
        $user = User::factory()->create();
        $song = Song::factory()->create();

        Vote::factory()->create([
            'user_id' => $user->id,
            'song_id' => $song->id,
            'vote_date' => now()->subDay()->toDateString(),
        ]);

        $response = $this->actingAs($user)->post(route('votes.store', $song));

        $response->assertSessionHas('status', 'Your vote has been recorded.');
        $this->assertSame(2, Vote::query()->count());
    }

    public function test_inactive_song_cannot_receive_votes(): void
    {
        $user = User::factory()->create();
        $song = Song::factory()->inactive()->create();

        $response = $this->actingAs($user)->post(route('votes.store', $song));

        $response->assertNotFound();
        $this->assertSame(0, Vote::query()->count());
    }

    public function test_voting_disabled_song_cannot_receive_votes(): void
    {
        $user = User::factory()->create();
        $song = Song::factory()->votingDisabled()->create();

        $response = $this->actingAs($user)->post(route('votes.store', $song));

        $response->assertSessionHas('error');
        $this->assertSame(0, Vote::query()->count());
    }

    public function test_votes_are_rate_limited(): void
    {
        $user = User::factory()->create();
        $songs = Song::factory()->count(21)->create();

        foreach ($songs->take(20) as $song) {
            $this->actingAs($user)->post(route('votes.store', $song));
        }

        $response = $this->actingAs($user)->post(route('votes.store', $songs->last()));

        $response->assertStatus(429);
    }

    public function test_concurrent_duplicate_votes_are_rejected_gracefully(): void
    {
        $user = User::factory()->create();
        $song = Song::factory()->create();

        Vote::query()->create([
            'user_id' => $user->id,
            'song_id' => $song->id,
            'vote_date' => now()->toDateString(),
        ]);

        $result = (new CastVote)($user, $song);

        $this->assertSame(VoteResult::AlreadyVoted, $result);
        $this->assertSame(1, Vote::query()->count());
    }
}
