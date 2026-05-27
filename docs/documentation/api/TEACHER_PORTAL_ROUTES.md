# Rutas — Portal docente

Prefijo: `/teacher` — middleware `role:Docente|Administrador`.

| Área | Rutas ejemplo |
|------|----------------|
| Dashboard | `/teacher/dashboard` |
| LMS | `/teacher/classrooms`, CRUD tareas/exámenes |
| Diagnósticos | `/teacher/diagnostics` |
| Riesgo / IA | `/teacher/academic-risk`, `/teacher/ai-insights` |
| Copiloto | `/teacher/ai-copilot`, `/exams`, `/assignments`, `/rubrics` |
| Reuniones | `/teacher/meetings` |
| Analítica | `/teacher/analytics` |

Políticas verifican asignación a sección del año activo.
