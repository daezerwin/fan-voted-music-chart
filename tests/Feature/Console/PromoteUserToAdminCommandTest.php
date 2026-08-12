<?php

namespace Tests\Feature\Console;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoteUserToAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_promotes_an_existing_user_to_admin(): void
    {
        $user = User::factory()->create(['email' => 'future-admin@example.com']);

        $this->artisan('users:promote-admin', ['email' => 'future-admin@example.com'])
            ->assertExitCode(0);

        $this->assertSame(UserRole::Admin, $user->fresh()->role);
    }

    public function test_it_fails_gracefully_for_an_unknown_email(): void
    {
        $this->artisan('users:promote-admin', ['email' => 'nobody@example.com'])
            ->assertExitCode(1);
    }

    public function test_it_is_idempotent_for_an_already_promoted_admin(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'admin@example.com']);

        $this->artisan('users:promote-admin', ['email' => 'admin@example.com'])
            ->assertExitCode(0);

        $this->assertSame(UserRole::Admin, $admin->fresh()->role);
    }
}
