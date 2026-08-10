<?php

namespace Tests\Feature\Admin;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtistManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_ordinary_user_cannot_manage_artists(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.artists.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.artists.create'))->assertForbidden();
    }

    public function test_admin_can_create_an_artist_with_an_auto_generated_slug(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.artists.store'), [
            'name' => 'The Test Band',
            'is_active' => '1',
        ]);

        $artist = Artist::query()->where('name', 'The Test Band')->firstOrFail();

        $response->assertRedirect(route('admin.artists.edit', $artist));
        $this->assertSame('the-test-band', $artist->slug);
        $this->assertTrue($artist->is_active);
    }

    public function test_admin_can_update_an_artist(): void
    {
        $admin = User::factory()->admin()->create();
        $artist = Artist::factory()->create(['is_active' => true, 'is_featured' => false]);

        $response = $this->actingAs($admin)->put(route('admin.artists.update', $artist), [
            'name' => 'Updated Name',
            'is_featured' => '1',
        ]);

        $response->assertRedirect(route('admin.artists.edit', $artist));
        $artist->refresh();
        $this->assertSame('Updated Name', $artist->name);
        $this->assertTrue($artist->is_featured);
        $this->assertFalse($artist->is_active);
    }

    public function test_artist_slug_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        Artist::factory()->create(['slug' => 'taken-slug']);

        $response = $this->actingAs($admin)->post(route('admin.artists.store'), [
            'name' => 'Another Artist',
            'slug' => 'taken-slug',
        ]);

        $response->assertSessionHasErrors('slug');
    }
}
