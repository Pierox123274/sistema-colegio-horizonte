<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Proveedor por defecto
    |--------------------------------------------------------------------------
    */
    'default_provider' => env('MEETING_PROVIDER', 'google_meet'),

    'google_meet' => [
        'enabled' => env('MEETING_GOOGLE_ENABLED', true),
        'base_url' => env('MEETING_GOOGLE_BASE_URL', 'https://meet.google.com'),
        /** Sala fija institucional; solo se usa si el docente no pegó enlace (fallback). */
        'configured_room_code' => env('MEETING_GOOGLE_ROOM_CODE'),
        'allow_generated_fallback' => env('MEETING_ALLOW_GOOGLE_FALLBACK', true),
        'api_ready' => filter_var(env('MEETING_GOOGLE_API_READY', false), FILTER_VALIDATE_BOOLEAN),
        'client_id' => env('MEETING_GOOGLE_CLIENT_ID'),
        'client_secret' => env('MEETING_GOOGLE_CLIENT_SECRET'),
    ],

    'zoom' => [
        'enabled' => env('MEETING_ZOOM_ENABLED', false),
        'base_url' => env('MEETING_ZOOM_BASE_URL', 'https://zoom.us/j'),
        'api_ready' => filter_var(env('MEETING_ZOOM_API_READY', false), FILTER_VALIDATE_BOOLEAN),
        'account_id' => env('MEETING_ZOOM_ACCOUNT_ID'),
        'client_id' => env('MEETING_ZOOM_CLIENT_ID'),
        'client_secret' => env('MEETING_ZOOM_CLIENT_SECRET'),
    ],

    'teams' => [
        'enabled' => env('MEETING_TEAMS_ENABLED', false),
        'base_url' => env('MEETING_TEAMS_BASE_URL', 'https://teams.microsoft.com/l/meetup-join'),
        'api_ready' => filter_var(env('MEETING_TEAMS_API_READY', false), FILTER_VALIDATE_BOOLEAN),
        'tenant_id' => env('MEETING_TEAMS_TENANT_ID'),
    ],

    'reminder_minutes_before' => (int) env('MEETING_REMINDER_MINUTES', 30),

];
