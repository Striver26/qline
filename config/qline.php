<?php

return [

    /*
    |--------------------------------------------------------------------------
    | BillPlz Payment Gateway
    |--------------------------------------------------------------------------
    */
    'billplz' => [
        'secret' => env('BILLPLZ_SECRET'),
        'collection_id' => env('BILLPLZ_COLLECTION_ID'),
        'x_signature' => env('BILLPLZ_X_SIGNATURE'),
        'sandbox' => env('BILLPLZ_SANDBOX', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Meta WhatsApp Cloud API
    |--------------------------------------------------------------------------
    */
    'meta' => [
        'token' => env('META_WHATSAPP_TOKEN'),
        'phone_number_id' => env('META_PHONE_NUMBER_ID'),
        'webhook_verify_token' => env('META_WEBHOOK_VERIFY_TOKEN', 'qline_secret'),
        'app_secret' => env('META_APP_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Tiers
    |--------------------------------------------------------------------------
    | free      = RM0         — limited queue usage, one service point
    | daily     = RM99/day    — daily pass with three service point
    | monthly   = RM69/month  — growth plan with monthly/yearly billing
    | advanced  = RM149/month — scale plan with monthly/yearly billing
    */
    'tiers' => [
        'free' => [
            'price' => 0.00,
            'yearly_price' => 0.00,
            'label' => 'Free',
            'daily_limit' => 50,
            'service_point_limit' => 1,
            'service_points' => true,
            'billing_cycle' => 'free',
        ],
        'daily' => [
            'price' => 99.00,
            'label' => 'Daily',
            'daily_limit' => 0,
            'service_point_limit' => 3,
            'service_points' => true,
            'billing_cycle' => 'daily',
        ],
        'monthly' => [
            'price' => 69.00,
            'yearly_price' => 528.00,
            'label' => 'Growth',
            'daily_limit' => 500,
            'service_point_limit' => 5,
            'service_points' => true,
            'billing_cycle' => 'monthly',
        ],
        'advanced' => [
            'price' => 149.00,
            'yearly_price' => 1142.40,
            'label' => 'Scale',
            'daily_limit' => 0, // 0 = unlimited
            'service_point_limit' => 0, // 0 = unlimited
            'service_points' => true,
            'billing_cycle' => 'monthly',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */
    'currency' => env('QLINE_CURRENCY', 'MYR'),
    'currency_symbol' => env('QLINE_CURRENCY_SYMBOL', 'RM'),

];
