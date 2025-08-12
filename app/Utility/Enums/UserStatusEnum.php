<?php

namespace App\Utility\Enums;

enum UserStatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BANNED = 'banned';

    public static function getValues(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
