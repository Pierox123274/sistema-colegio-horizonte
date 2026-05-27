<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Integraciones externas — feature flags globales
    |--------------------------------------------------------------------------
    */
    'enabled' => filter_var(env('INTEGRATIONS_ENABLED', true), FILTER_VALIDATE_BOOLEAN),

    'calendar' => [
        'enabled' => filter_var(env('INTEGRATION_CALENDAR_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
        'provider' => env('INTEGRATION_CALENDAR_PROVIDER', 'google'),
    ],

    'messaging' => [
        'enabled' => filter_var(env('INTEGRATION_MESSAGING_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
        'provider' => env('INTEGRATION_MESSAGING_PROVIDER', 'whatsapp'),
    ],

    'payments' => [
        'enabled' => filter_var(env('INTEGRATION_PAYMENTS_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
        'provider' => env('INTEGRATION_PAYMENTS_PROVIDER', 'mercadopago'),
    ],

    'push' => [
        'enabled' => filter_var(env('INTEGRATION_PUSH_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
        'provider' => env('INTEGRATION_PUSH_PROVIDER', 'firebase'),
    ],

    'storage' => [
        'external_enabled' => filter_var(env('INTEGRATION_EXTERNAL_STORAGE', false), FILTER_VALIDATE_BOOLEAN),
    ],

    'webhooks' => [
        'enabled' => filter_var(env('INTEGRATION_WEBHOOKS_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
        'signature_header' => env('INTEGRATION_WEBHOOK_SIGNATURE_HEADER', 'X-Webhook-Signature'),
        'payment_secret' => env('INTEGRATION_WEBHOOK_PAYMENT_SECRET'),
        'calendar_secret' => env('INTEGRATION_WEBHOOK_CALENDAR_SECRET'),
        'max_replay_attempts' => (int) env('INTEGRATION_WEBHOOK_MAX_REPLAY', 3),
    ],

    'mail' => [
        'delivery_log_enabled' => filter_var(env('MAIL_DELIVERY_LOG_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
        'preview_enabled' => filter_var(env('MAIL_PREVIEW_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
        'max_retries' => (int) env('MAIL_MAX_RETRIES', 3),
    ],

];
