<?php

return [

    'default' => env('PUSH_PROVIDER', 'firebase'),

    'firebase' => [
        'enabled' => filter_var(env('FIREBASE_PUSH_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'credentials_path' => env('FIREBASE_CREDENTIALS_PATH'),
        'server_key' => env('FIREBASE_SERVER_KEY'),
    ],

];
