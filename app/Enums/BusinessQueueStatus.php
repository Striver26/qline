<?php

namespace App\Enums;

enum BusinessQueueStatus: string
{
    case OPEN = 'open';
    case PAUSED = 'paused';
    case CLOSED = 'closed';
}
