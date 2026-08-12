<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;

class PromoteUserToAdminCommand extends Command
{
    protected $signature = 'users:promote-admin {email : Email address of an existing user}';

    protected $description = 'Promote an existing user to the admin role — the way to bootstrap the first admin on a fresh deploy.';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            $this->error("No user found with email [{$email}]. They need to sign up (via /register or Facebook) first.");

            return self::FAILURE;
        }

        if ($user->role === UserRole::Admin) {
            $this->info("{$user->email} is already an admin.");

            return self::SUCCESS;
        }

        $user->forceFill(['role' => UserRole::Admin])->save();

        $this->info("{$user->email} is now an admin. They can reach the admin area at /admin.");

        return self::SUCCESS;
    }
}
