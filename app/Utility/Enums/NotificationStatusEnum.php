<?php

namespace App\Utility\Enums;

enum NotificationStatusEnum: string
{
    case SENT = 'sent';
    case PENDING = 'pending';
    case SCHEDULED = 'scheduled';

    public static function getValues(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
