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

    /*
    |--------------------------------------------------------------------------
    | Sincronización de notas LMS → libro de calificaciones
    |--------------------------------------------------------------------------
    |
    | Al calificar tareas o completar exámenes online, se crea/actualiza un
    | Evaluation vinculado y un GradeRecord por estudiante.
    |
    */
    'grade_sync' => [
        'enabled' => (bool) env('LMS_SYNC_GRADES', true),
        'period' => env('LMS_GRADE_SYNC_PERIOD', 'LMS'),
        'default_weight' => (float) env('LMS_GRADE_SYNC_WEIGHT', 1),
        'online_exam_max_score' => (float) env('LMS_ONLINE_EXAM_MAX_SCORE', 20),
    ],
];
