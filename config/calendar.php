<?php

return [

    'default' => env('CALENDAR_PROVIDER', 'google'),

    'google' => [
        'enabled' => filter_var(env('GOOGLE_CALENDAR_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
        'client_id' => env('GOOGLE_CALENDAR_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CALENDAR_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_CALENDAR_REDIRECT_URI'),
        'calendar_id' => env('GOOGLE_CALENDAR_ID', 'primary'),
        'oauth_ready' => filter_var(env('GOOGLE_CALENDAR_OAUTH_READY', false), FILTER_VALIDATE_BOOLEAN),
    ],

    'export' => [
        'timezone' => env('CALENDAR_EXPORT_TIMEZONE', 'America/Lima'),
        'template_url' => 'https://calendar.google.com/calendar/render',
    ],

];
