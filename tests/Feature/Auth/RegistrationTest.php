<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_is_accessible(): void
    {
        $this->get(route('register'))->assertOk();
    }

    public function test_user_can_register_with_a_hashed_password(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'jane@example.com')->firstOrFail();
        $this->assertNotSame('SecurePass123', $user->password);
        $this->assertTrue(Hash::check('SecurePass123', $user->password));
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post(route('register.store'), [
            'name' => 'Jane Doe',
            'email' => 'taken@example.com',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_password_confirmation_must_match(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SomethingElse',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_password_must_meet_minimum_requirements(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_authenticated_user_cannot_view_registration_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('register'))->assertRedirect(route('home'));
    }
}
