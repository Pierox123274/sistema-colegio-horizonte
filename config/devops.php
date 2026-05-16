<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Retención de auditoría y sesiones (días)
    |--------------------------------------------------------------------------
    */
    'audit_log_retention_days' => (int) env('DEVOPS_AUDIT_LOG_RETENTION_DAYS', 365),

    'user_session_retention_days' => (int) env('DEVOPS_USER_SESSION_RETENTION_DAYS', 90),

    /*
    |--------------------------------------------------------------------------
    | Respaldos (storage/app/backups)
    |--------------------------------------------------------------------------
    */
    'backup_max_files' => (int) env('DEVOPS_BACKUP_MAX_FILES', 14),

    'backup_include_public' => filter_var(env('DEVOPS_BACKUP_INCLUDE_PUBLIC', true), FILTER_VALIDATE_BOOL),

    /*
    |--------------------------------------------------------------------------
    | Correos institucionales automáticos
    |--------------------------------------------------------------------------
    */
    'operations_email' => env('DEVOPS_OPERATIONS_EMAIL'),

    'send_daily_summary' => filter_var(env('DEVOPS_SEND_DAILY_SUMMARY', false), FILTER_VALIDATE_BOOL),

    'send_welcome_email' => filter_var(env('DEVOPS_SEND_WELCOME_EMAIL', false), FILTER_VALIDATE_BOOL),

    /*
    |--------------------------------------------------------------------------
    | IA futura — snapshot de métricas (clave de caché)
    |--------------------------------------------------------------------------
    */
    'metrics_cache_key' => env('DEVOPS_METRICS_CACHE_KEY', 'institution.metrics.snapshot'),

    'metrics_cache_ttl_seconds' => (int) env('DEVOPS_METRICS_CACHE_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Ruta al binario mysqldump (solo MySQL)
    |--------------------------------------------------------------------------
    */
    'mysqldump_path' => env('DEVOPS_MYSQLDUMP_PATH', 'mysqldump'),

];
