<?php

namespace App\Enums;

enum RewardStatus: string
{
    case AVAILABLE = 'available';
    case REDEEMED = 'redeemed';
}
