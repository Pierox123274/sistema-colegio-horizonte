# Rutas — Portal estudiante

Prefijo: `/student` — middleware `role:Estudiante|Administrador`.

| Ruta | Propósito |
|------|-----------|
| `/student/dashboard` | Inicio |
| `/student/grades` | Notas |
| `/student/attendance` | Asistencia |
| `/student/classrooms` | LMS |
| `/student/ai-tutor` | Tutor IA |
| `/student/ai-tutor/*` | Coach (summary, mini-quiz, …) |
| `/student/recommendations` | Recomendaciones |
| `/student/meetings` | Reuniones |
| `/student/diagnostic/*` | Diagnósticos |
| `/student/gamification` | Gamificación |

Throttling `ai` en rutas de IA.
