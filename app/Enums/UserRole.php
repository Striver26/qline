<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPERADMIN = 'superadmin';
    case PLATFORM_STAFF = 'platform_staff';
    case BUSINESS_OWNER = 'business_owner';
    case BUSINESS_STAFF = 'business_staff';
}
