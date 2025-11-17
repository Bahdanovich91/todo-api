<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskStatus: string
{
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    case PENDING = 'pending';
    case DONE = 'done';
}
