# Arquitectura LMS — Aula virtual

## Propósito

Gestionar **aulas virtuales** por docente/sección, tareas con entregas, exámenes online y calendario académico vinculado.

## Componentes

- `VirtualClassroom`, `Assignment`, `AssignmentSubmission`  
- `OnlineExam`, `OnlineExamQuestion`, `OnlineExamAttempt`  
- `AcademicCalendarEvent`  
- Servicios: `LMSService`, `AssignmentService`, `OnlineExamService`, `LMSCalendarService`, `LMSAdaptiveIntegrationService`  
- Políticas: `VirtualClassroomPolicy`, `AssignmentPolicy`, `OnlineExamPolicy`

## Flujo docente

Crear aula → publicar tarea/examen → estudiantes entregan o rinden → calificación → integración adaptativa si puntaje bajo (recomendaciones/debilidades).

## Flujo estudiante

Listar aulas matriculadas → ver tareas pendientes → subir entrega → intentar examen con ventana temporal.

## Tecnologías

Inertia (Teacher/Student), almacenamiento de archivos en `storage`, colas para notificaciones.

## Decisiones técnicas

- Integración con **Fase 22** vía reglas locales (`LMSAdaptiveIntegrationService`), no dependiente de IA externa.  
- Calendario unificado con eventos de tipo reunión, tarea, examen.

## Relación con otros módulos

- **Meetings** — videoclases ligadas a aula o sección.  
- **IA Fase 31** — export de tareas generadas al LMS.  
- **Gamificación** — XP por entregas y uso de LMS.
