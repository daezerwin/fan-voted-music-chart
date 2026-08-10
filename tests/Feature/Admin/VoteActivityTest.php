<?php

namespace Tests\Feature\Admin;

use App\Models\Song;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoteActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_todays_voting_activity(): void
    {
        $admin = User::factory()->admin()->create();
        $voter = User::factory()->create(['name' => 'Prolific Voter']);
        $song = Song::factory()->create();

        Vote::factory()->create([
            'user_id' => $voter->id,
            'song_id' => $song->id,
            'vote_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.votes.index'));

        $response->assertOk();
        $response->assertSee('Prolific Voter');
    }

    public function test_ordinary_user_cannot_view_voting_activity(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.votes.index'))->assertForbidden();
    }

    public function test_shared_ip_addresses_are_surfaced(): void
    {
        $admin = User::factory()->admin()->create();
        $songs = Song::factory()->count(2)->create();
        $date = now()->toDateString();

        Vote::factory()->create(['song_id' => $songs[0]->id, 'vote_date' => $date, 'ip_address' => '203.0.113.9']);
        Vote::factory()->create(['song_id' => $songs[1]->id, 'vote_date' => $date, 'ip_address' => '203.0.113.9']);

        $response = $this->actingAs($admin)->get(route('admin.votes.index'));

        $response->assertOk();
        $response->assertSee('203.0.113.9');
        $response->assertSee('2 accounts');
    }

    public function test_ip_used_by_a_single_account_is_not_flagged(): void
    {
        $admin = User::factory()->admin()->create();
        $voter = User::factory()->create();
        Vote::factory()->create(['user_id' => $voter->id, 'vote_date' => now()->toDateString(), 'ip_address' => '203.0.113.9']);

        $response = $this->actingAs($admin)->get(route('admin.votes.index'));

        $response->assertOk();
        $response->assertSee('No shared IPs on this date.');
    }
}
