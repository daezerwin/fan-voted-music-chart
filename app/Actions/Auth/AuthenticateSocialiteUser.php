<?php

namespace App\Actions\Auth;

use App\Enums\AccountStatus;
use App\Models\User;
use App\Models\UserIdentity;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class AuthenticateSocialiteUser
{
    /**
     * Find the local user matching a Socialite identity, linking or creating
     * an account as needed. Never persists the OAuth access/refresh token.
     */
    public function __invoke(string $provider, SocialiteUser $socialiteUser): User
    {
        $identity = UserIdentity::query()
            ->where('provider', $provider)
            ->where('provider_id', $socialiteUser->getId())
            ->first();

        if ($identity !== null) {
            return $identity->user;
        }

        $email = $socialiteUser->getEmail();

        $user = $email !== null
            ? User::query()->where('email', $email)->first()
            : null;

        return DB::transaction(function () use ($provider, $socialiteUser, $email, $user) {
            $user ??= User::query()->forceCreate([
                'name' => $socialiteUser->getName() ?: $socialiteUser->getNickname() ?: 'Music Fan',
                'email' => $email ?? $this->placeholderEmail($provider, $socialiteUser->getId()),
                'email_verified_at' => $email !== null ? now() : null,
                'avatar' => $socialiteUser->getAvatar(),
                'status' => AccountStatus::Active,
            ]);

            $user->identities()->create([
                'provider' => $provider,
                'provider_id' => $socialiteUser->getId(),
            ]);

            return $user;
        });
    }

    private function placeholderEmail(string $provider, string $providerId): string
    {
        return "{$provider}-{$providerId}@no-email.invalid";
    }
}
