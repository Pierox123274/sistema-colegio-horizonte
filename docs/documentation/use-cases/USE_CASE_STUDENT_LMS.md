# Caso de uso — Estudiante en el LMS (aula virtual)

## Identificación

| Campo | Valor |
|-------|--------|
| **ID** | UC-LMS-001 |
| **Nombre** | Acceder al aula virtual y completar actividades |
| **Módulo** | LMS |

## Actores

| Tipo | Actor |
|------|--------|
| **Principal** | Estudiante (`IntranetRole::Estudiante`) |
| **Secundarios** | Docente (publica contenido), Sistema (control de acceso, intentos de examen) |

## Objetivo

Permitir al estudiante consultar sus aulas virtuales asignadas, revisar recursos, entregar tareas y rendir exámenes en línea dentro de los plazos definidos por el docente.

## Precondiciones

1. El estudiante posee cuenta activa vinculada a un registro en `students`.
2. Existe matrícula vigente y el docente ha asignado al estudiante a una sección con aula virtual (`virtual_classrooms`).
3. El usuario ha iniciado sesión en `/login` y accede al portal `/student/*`.

## Flujo principal

1. El estudiante ingresa al **Dashboard** (`/student/dashboard`).
2. Navega a **Mis aulas** (`/student/classrooms`).
3. Selecciona un aula (`/student/classrooms/{id}`).
4. Revisa anuncios, recursos y tareas publicadas.
5. Para una tarea: abre el detalle, adjunta o redacta la entrega y confirma envío (`AssignmentSubmission`).
6. Para un examen disponible: inicia intento (`/student/classrooms/.../exams/...`), responde preguntas y envía.
7. El sistema registra la entrega o el intento y muestra confirmación en pantalla.

## Flujos alternativos

| ID | Condición | Comportamiento |
|----|-----------|----------------|
| FA-1 | Examen con límite de intentos alcanzado | Se muestra estado “sin intentos disponibles”; no se permite nuevo inicio. |
| FA-2 | Tarea fuera de fecha límite | La entrega puede rechazarse o marcarse como tardía según reglas del servicio `AssignmentService`. |
| FA-3 | Estudiante sin aulas asignadas | Lista vacía con componente `AppEmptyState` y mensaje orientador. |

## Excepciones

| ID | Excepción | Respuesta del sistema |
|----|-----------|------------------------|
| EX-1 | Sesión expirada | Redirección a login. |
| EX-2 | Acceso a aula de otra sección | HTTP 403; política `VirtualClassroomAccessService`. |
| EX-3 | Error al guardar entrega | Mensaje de error Inertia; datos no persistidos. |

## Postcondiciones y resultado esperado

- La entrega queda registrada con timestamp y usuario.
- El intento de examen queda en estado definido (`OnlineExamAttempt`).
- El docente puede calificar o revisar desde su portal.
- **Resultado esperado:** el estudiante completa actividades académicas digitales sin salir del ecosistema institucional.

## Evidencia técnica

- Rutas: `routes/web.php` — grupo `student/classrooms`.
- Servicios: `LMSService`, `AssignmentService`, `OnlineExamService`.
- Pruebas: `tests/Feature/LMS/VirtualClassroomTest.php`, `tests/Feature/Student/StudentPortalTest.php`, Cypress `virtual-classroom.cy.ts`.
