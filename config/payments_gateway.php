<?php

return [

    'default' => env('PAYMENT_GATEWAY_PROVIDER', 'mercadopago'),

    'mercadopago' => [
        'enabled' => filter_var(env('MERCADOPAGO_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
        'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
        'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
        'webhook_secret' => env('MERCADOPAGO_WEBHOOK_SECRET'),
        'sandbox' => filter_var(env('MERCADOPAGO_SANDBOX', true), FILTER_VALIDATE_BOOLEAN),
    ],

    'culqi' => [
        'enabled' => filter_var(env('CULQI_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
        'public_key' => env('CULQI_PUBLIC_KEY'),
        'secret_key' => env('CULQI_SECRET_KEY'),
        'webhook_secret' => env('CULQI_WEBHOOK_SECRET'),
    ],

    'checkout' => [
        'success_url' => env('PAYMENT_CHECKOUT_SUCCESS_URL'),
        'cancel_url' => env('PAYMENT_CHECKOUT_CANCEL_URL'),
        'webhook_url' => env('PAYMENT_WEBHOOK_URL'),
    ],

];
