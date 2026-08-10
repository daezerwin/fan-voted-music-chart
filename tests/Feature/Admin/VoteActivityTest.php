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
}
