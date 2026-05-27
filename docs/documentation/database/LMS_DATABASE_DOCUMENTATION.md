# Base de datos — LMS

## virtual_classrooms

Aula por docente/sección/año; `teacher_user_id`, título, estado activo.

## assignments + assignment_submissions

Tarea, fecha límite, entrega de estudiante, calificación, feedback docente.

## online_exams + online_exam_questions + online_exam_attempts

Exámenes con ventana temporal y intentos calificados.

## academic_calendar_events

Eventos unificados (tareas, exámenes, reuniones) con `related_type` polimórfico opcional.
