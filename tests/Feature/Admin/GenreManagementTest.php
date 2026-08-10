<?php

namespace Tests\Feature\Admin;

use App\Models\Genre;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_delete_an_unused_genre(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.genres.store'), ['name' => 'Synthwave']);
        $genre = Genre::query()->where('name', 'Synthwave')->firstOrFail();

        $response = $this->actingAs($admin)->delete(route('admin.genres.destroy', $genre));

        $response->assertRedirect(route('admin.genres.index'));
        $this->assertDatabaseMissing('genres', ['id' => $genre->id]);
    }

    public function test_genre_with_songs_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $genre = Genre::factory()->create();
        Song::factory()->create(['genre_id' => $genre->id]);

        $response = $this->actingAs($admin)->delete(route('admin.genres.destroy', $genre));

        $response->assertRedirect(route('admin.genres.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('genres', ['id' => $genre->id]);
    }
}
