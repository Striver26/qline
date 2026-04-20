<?php

namespace App\Enums;

enum QueueStatus: string
{
    case WAITING = 'waiting';
    case CALLED = 'called';
    case SERVING = 'serving';
    case COMPLETED = 'completed';
    case SKIPPED = 'skipped';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';

    public function getLabel(): string
    {
        return match($this) {
            self::WAITING => 'Waiting',
            self::CALLED => 'Called',
            self::SERVING => 'Being Served',
            self::COMPLETED => 'Completed',
            self::SKIPPED => 'Skipped',
            self::CANCELLED => 'Cancelled',
            self::NO_SHOW => 'No Show',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::WAITING => 'text-amber-400',
            self::CALLED => 'text-teal-400',
            self::SERVING => 'text-blue-400',
            self::COMPLETED => 'text-emerald-400',
            self::SKIPPED => 'text-orange-400',
            self::CANCELLED, self::NO_SHOW => 'text-red-400',
        };
    }
}
