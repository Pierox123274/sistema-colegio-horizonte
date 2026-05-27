# Arquitectura ERP — Gestión escolar administrativa

## Propósito

Módulos de **gestión institucional**: personas, matrícula, finanzas, inventario, ventas y reportes académicos básicos.

## Componentes principales

- **Estudiantes y apoderados** — `Student`, `Guardian`, vínculos familiares.  
- **Estructura académica** — niveles, grados, secciones, años lectivos, materias.  
- **Matrículas** — `Enrollment`, estados, asignación a sección.  
- **Asistencia** — `Attendance` por estudiante/fecha.  
- **Notas** — `Evaluation`, `GradeRecord`.  
- **Finanzas** — `PaymentConcept`, `Pension`, `Payment`, comprobantes.  
- **Inventario y ventas** — productos, movimientos, caja, ventas de uniformes/libros.  
- **Analítica intranet** — dashboards agregados para administración.

## Flujo

Secretaría/admin registran datos → políticas por rol → servicios de dominio (`EnrollmentService`, `PaymentService`, etc.) → auditoría en operaciones sensibles.

## Responsabilidades

| Rol | Acceso típico |
|-----|----------------|
| Administrador | Total en configuración y reportes |
| Secretaría | Operación diaria matrícula, pagos, asistencia |
| Docente | Consulta alumnos de sus secciones, notas propias |

## Tecnologías

Eloquent, Policies, Form Requests, PDF/boletas donde aplique.

## Decisiones técnicas

- Año académico **activo** como contexto transversal para matrículas y asignaciones docentes.  
- Separación entre **concepto de pago**, **pensión** y **pago** registrado.

## Relación con otros módulos

- Alimenta **LMS** (matrícula → aulas).  
- Alimenta **adaptive** y **IA** (métricas agregadas de notas/asistencia).  
- **CMS** independiente del ERP pero comparte branding institucional.
