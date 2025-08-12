<?php

namespace App\Utility\Enums;

enum NotificationTypeEnum: string
{
    case ALL = 'all';
    case SPECIFIC_USER = 'specific_user';

    public static function getValues(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
