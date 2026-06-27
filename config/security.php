<?php

return [

    'login_max_attempts' => (int) env('SECURITY_LOGIN_MAX_ATTEMPTS', 5),

    'login_lockout_minutes' => (int) env('SECURITY_LOGIN_LOCKOUT_MINUTES', 15),

    'session_lifetime_minutes' => (int) env('SESSION_LIFETIME', 120),

    'suspicious_ip_attempts' => (int) env('SECURITY_SUSPICIOUS_IP_ATTEMPTS', 20),

    'suspicious_ip_window_minutes' => (int) env('SECURITY_SUSPICIOUS_IP_WINDOW', 60),

    /*
    |--------------------------------------------------------------------------
    | Cifrado de datos personales (AES-256-CBC vía APP_KEY)
    |--------------------------------------------------------------------------
    */
    'encrypt_personal_data' => (bool) env('SECURITY_ENCRYPT_PERSONAL_DATA', true),

    'encrypt_sessions' => (bool) env('SESSION_ENCRYPT', false),

];
