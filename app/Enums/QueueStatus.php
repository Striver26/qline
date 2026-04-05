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
}
