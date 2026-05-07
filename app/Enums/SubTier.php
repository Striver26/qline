<?php

namespace App\Enums;

enum SubTier: string
{
    case FREE = 'free';
    case DAILY = 'daily';
    case MONTHLY = 'monthly';
    case ADVANCED = 'advanced';
}
