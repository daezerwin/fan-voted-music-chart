<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Moderator = 'moderator';
    case Admin = 'admin';

    public function isAtLeast(self $role): bool
    {
        $order = [self::User, self::Moderator, self::Admin];

        return array_search($this, $order, true) >= array_search($role, $order, true);
    }
}
