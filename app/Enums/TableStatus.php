<?php

namespace App\Enums;

enum TableStatus: string
{
    case FREE = 'free';
    case OCCUPIED = 'occupied';

    public function label(): string
    {
        return match ($this) {
            self::FREE => 'Free',
            self::OCCUPIED => 'Occupied',
        };
    }
}
