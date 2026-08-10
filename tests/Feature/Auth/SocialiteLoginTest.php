<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\UserIdentity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class SocialiteLoginTest extends TestCase
{
    use RefreshDatabase;

    private function fakeFacebookUser(string $id, ?string $email, string $name = 'Jane Doe'): void
    {
        $socialiteUser = new SocialiteUser;
        $socialiteUser->map([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('facebook')->andReturn($provider);
    }

    public function test_new_facebook_user_is_created_and_logged_in(): void
    {
        $this->fakeFacebookUser('fb-123', 'jane@example.com');

        $response = $this->get('/auth/facebook/callback');

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'jane@example.com')->firstOrFail();
        $this->assertTrue($user->identities()->where('provider', 'facebook')->where('provider_id', 'fb-123')->exists());
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_returning_facebook_identity_logs_in_the_same_user(): void
    {
        $user = User::factory()->create(['email' => 'existing@example.com']);
        UserIdentity::query()->create([
            'user_id' => $user->id,
            'provider' => 'facebook',
            'provider_id' => 'fb-999',
        ]);

        $this->fakeFacebookUser('fb-999', 'existing@example.com');

        $this->get('/auth/facebook/callback');

        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, User::query()->count());
    }

    public function test_matching_email_links_a_new_identity_instead_of_duplicating_the_user(): void
    {
        $user = User::factory()->create(['email' => 'linked@example.com']);

        $this->fakeFacebookUser('fb-777', 'linked@example.com');

        $this->get('/auth/facebook/callback');

        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, User::query()->count());
        $this->assertTrue($user->identities()->where('provider_id', 'fb-777')->exists());
    }

    public function test_missing_facebook_email_creates_a_placeholder_email(): void
    {
        $this->fakeFacebookUser('fb-no-email', null);

        $this->get('/auth/facebook/callback');

        $this->assertAuthenticated();

        $user = User::query()->where('name', 'Jane Doe')->firstOrFail();
        $this->assertStringContainsString('@no-email.invalid', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_unsupported_provider_is_not_found(): void
    {
        $response = $this->get('/auth/google/redirect');

        $response->assertNotFound();
    }

    public function test_facebook_redirect_returns_a_redirect_response(): void
    {
        $response = $this->get('/auth/facebook/redirect');

        $response->assertRedirect();
        $this->assertStringContainsString('facebook.com', $response->headers->get('Location'));
    }

    public function test_user_can_sign_out(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
