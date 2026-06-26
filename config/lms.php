<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Límite de subida de entregas (KB)
    |--------------------------------------------------------------------------
    |
    | Valor en kilobytes para archivos adjuntos de tareas del aula virtual.
    | Por defecto 5 MB (5120 KB).
    |
    */
    'assignment_upload_max_kb' => (int) env('LMS_ASSIGNMENT_UPLOAD_MAX_KB', 5120),
];
