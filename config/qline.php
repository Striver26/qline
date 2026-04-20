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
    | daily     = RM10/day   — limited daily queue limit, no counters
    | monthly   = RM300/month — same as daily features, monthly billing
    | advanced  = RM450/month — unlimited daily limit, counter support
    */
    'tiers' => [
        'daily' => [
            'price' => 10.00,
            'label' => 'Daily',
            'daily_limit' => 100,
            'counters' => false,
            'billing_cycle' => 'daily',
        ],
        'monthly' => [
            'price' => 300.00,
            'label' => 'Monthly',
            'daily_limit' => 500,
            'counters' => false,
            'billing_cycle' => 'monthly',
        ],
        'advanced' => [
            'price' => 450.00,
            'label' => 'Advanced',
            'daily_limit' => 0, // 0 = unlimited
            'counters' => true,
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
