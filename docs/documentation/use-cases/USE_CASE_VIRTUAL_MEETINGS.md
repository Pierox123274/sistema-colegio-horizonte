# Caso de uso — Videoclase / reunión virtual

## Identificación

| Campo | Valor |
|-------|--------|
| **ID** | UC-MEET-005 |
| **Nombre** | Programar y unirse a una videoclase |
| **Módulo** | Reuniones virtuales (Fase 30) |

## Actores

| Tipo | Actor |
|------|--------|
| **Principal** | Docente (creación), Estudiante (participación) |
| **Secundarios** | Administrador (supervisión), Plataforma externa (Meet/Zoom/Teams — enlace manual) |

## Objetivo

Coordinar sesiones sincrónicas con enlace de reunión, registro de asistencia y estado de la sesión.

## Precondiciones

1. Reunión creada en `virtual_meetings` con ventana horaria.
2. Participantes asociados a sección o aula correspondiente.

## Flujo principal

1. El docente crea reunión desde portal docente o admin (`/intranet/meetings` / rutas teacher).
2. Define título, horario, proveedor (manual) y URL de reunión.
3. Los estudiantes ven la reunión en `/student/meetings`.
4. Al iniciar la ventana, el estudiante usa **Unirse** (`JoinMeetingButton`) — abre URL externa.
5. Opcional: registro de asistencia en `meeting_attendances`.

## Flujos alternativos

| ID | Condición | Comportamiento |
|----|-----------|----------------|
| FA-1 | Sin API de videoconferencia | Enlace pegado manualmente; **preparado para integración futura** con APIs nativas. |
| FA-2 | Grabación referenciada | Metadatos en `meeting_recordings` si el admin/docente registra URL. |

## Excepciones

| ID | Excepción | Respuesta |
|----|-----------|-----------|
| EX-1 | Reunión cancelada | Estado actualizado; botón deshabilitado. |
| EX-2 | Estudiante no invitado | Sin acceso a detalle de reunión. |

## Resultado esperado

Continuidad pedagógica sincrónica complementaria al LMS asíncrono.

## Evidencia

- `tests/Feature/Meetings/VirtualMeetingsTest.php`, Cypress `virtual-meetings.cy.ts`.
