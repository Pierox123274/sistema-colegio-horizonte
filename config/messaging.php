<?php

return [

    'default' => env('MESSAGING_PROVIDER', 'whatsapp'),

    'whatsapp' => [
        'enabled' => filter_var(env('WHATSAPP_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
        'api_url' => env('WHATSAPP_API_URL'),
        'api_token' => env('WHATSAPP_API_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
    ],

    'use_cases' => [
        'payments' => filter_var(env('WHATSAPP_USE_PAYMENTS', false), FILTER_VALIDATE_BOOLEAN),
        'attendance' => filter_var(env('WHATSAPP_USE_ATTENDANCE', false), FILTER_VALIDATE_BOOLEAN),
        'assignments' => filter_var(env('WHATSAPP_USE_ASSIGNMENTS', false), FILTER_VALIDATE_BOOLEAN),
        'announcements' => filter_var(env('WHATSAPP_USE_ANNOUNCEMENTS', false), FILTER_VALIDATE_BOOLEAN),
        'emergencies' => filter_var(env('WHATSAPP_USE_EMERGENCIES', false), FILTER_VALIDATE_BOOLEAN),
    ],

];
