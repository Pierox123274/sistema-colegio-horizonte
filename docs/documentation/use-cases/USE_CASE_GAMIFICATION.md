# Caso de uso — Gamificación del aprendizaje

## Identificación

| Campo | Valor |
|-------|--------|
| **ID** | UC-GAM-004 |
| **Nombre** | Obtener experiencia e insignias por actividad académica |
| **Módulo** | Gamificación |

## Actores

| Tipo | Actor |
|------|--------|
| **Principal** | Estudiante |
| **Secundarios** | Sistema (`GamificationService`), Docente/Admin (configuración de retos — admin) |

## Objetivo

Motivar la participación mediante XP, niveles, rachas, logros y retos vinculados a eventos LMS y académicos.

## Precondiciones

1. Perfil de gamificación creado o inicializado para el estudiante.
2. Eventos disparadores configurados (entrega de tarea, examen, asistencia, etc.).

## Flujo principal

1. El estudiante realiza una acción elegible (p. ej. entrega de tarea).
2. `GamificationService` evalúa reglas y otorga XP (`experience_transactions`).
3. Se actualiza `gamification_profiles` (nivel, XP total).
4. Si aplica, se desbloquea logro (`student_achievements`).
5. El estudiante visualiza progreso en portal estudiante / panel gamificación admin.

## Flujos alternativos

| ID | Condición | Comportamiento |
|----|-----------|----------------|
| FA-1 | Reto activo con plazo | Progreso en `student_challenges`. |
| FA-2 | Tabla de líderes | Snapshot periódico en `leaderboard_snapshots`. |

## Excepciones

| ID | Excepción | Respuesta |
|----|-----------|-----------|
| EX-1 | Acción duplicada en ventana corta | Idempotencia o no doble XP según regla de negocio. |

## Resultado esperado

Retroalimentación visible del progreso y mayor engagement sin sustituir la calificación formal.

## Evidencia

- `tests/Feature/Gamification/GamificationTest.php`, Cypress `gamification.cy.ts`.
