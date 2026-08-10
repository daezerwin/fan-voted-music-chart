<?php

namespace Tests\Feature\Admin;

use App\Models\Song;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manually_regenerate_a_chart(): void
    {
        $admin = User::factory()->admin()->create();
        $song = Song::factory()->create();
        $date = now()->subDay()->toDateString();

        Vote::factory()->create(['song_id' => $song->id, 'vote_date' => $date]);

        $response = $this->actingAs($admin)->post(route('admin.charts.regenerate'), ['date' => $date]);

        $response->assertRedirect(route('admin.charts.index'));
        $this->assertDatabaseHas('chart_entries', ['song_id' => $song->id]);
    }

    public function test_future_dates_are_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.charts.regenerate'), [
            'date' => now()->addDay()->toDateString(),
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_ordinary_user_cannot_access_chart_tools(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.charts.index'))->assertForbidden();
    }
}
