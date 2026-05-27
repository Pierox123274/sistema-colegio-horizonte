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
    ],

    'zoom' => [
        'enabled' => env('MEETING_ZOOM_ENABLED', false),
        'base_url' => env('MEETING_ZOOM_BASE_URL', 'https://zoom.us/j'),
    ],

    'teams' => [
        'enabled' => env('MEETING_TEAMS_ENABLED', false),
        'base_url' => env('MEETING_TEAMS_BASE_URL', 'https://teams.microsoft.com/l/meetup-join'),
    ],

    'reminder_minutes_before' => (int) env('MEETING_REMINDER_MINUTES', 30),

];
