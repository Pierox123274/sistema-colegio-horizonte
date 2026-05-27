# Sistema Web Institucional e Intranet — I.E.P. Horizonte

## Nombre del proyecto

**Sistema Colegio Horizonte** — plataforma integral de gestión escolar, aprendizaje digital y presencia web institucional.

## Objetivo general

Digitalizar y unificar los procesos académicos, administrativos, financieros y de comunicación de una institución educativa privada (Inicial, Primaria y Secundaria), ofreciendo portales diferenciados por rol y cumpliendo estándares de seguridad, trazabilidad y calidad de software.

## Problema que resuelve

Las instituciones educativas suelen operar con:

- Datos dispersos (planillas, correos, hojas de cálculo).
- Poca visibilidad del rendimiento y la asistencia en tiempo real.
- Comunicación institucional fragmentada.
- Dificultad para escalar herramientas pedagógicas digitales (LMS, diagnósticos, tutoría).

Este sistema centraliza matrícula, notas, asistencia, finanzas, inventario, aula virtual, sitio web público, IA pedagógica asistida y operaciones de producción en una sola base tecnológica coherente.

## Usuarios del sistema

| Rol | Uso principal |
|-----|----------------|
| **Administrador** | Configuración global, usuarios, estructura académica, finanzas, CMS, seguridad, integraciones |
| **Secretaría** | Matrículas, pagos, asistencia, apoderados, operaciones administrativas |
| **Docente** | Aulas virtuales, diagnósticos, riesgo académico, copiloto IA, videoclases |
| **Estudiante** | Notas, asistencia, LMS, tutor IA, gamificación, reuniones |
| **Apoderado** | Rol previsto en arquitectura; portal dedicado según roadmap institucional |

## Módulos principales

1. **ERP escolar** — estudiantes, apoderados, matrículas, estructura académica, notas, asistencia.  
2. **Finanzas** — pensiones, pagos, caja, comprobantes.  
3. **Inventario y ventas** — uniformes, libros, movimientos de stock.  
4. **LMS** — aulas virtuales, tareas, exámenes online, calendario académico.  
5. **Aprendizaje adaptativo** — banco de preguntas, diagnósticos, rutas de aprendizaje.  
6. **IA institucional** — tutor estudiante, copiloto docente, analítica y riesgo académico.  
7. **CMS** — sitio web público administrable.  
8. **Comunicación** — comunicados y centro de notificaciones.  
9. **Gamificación** — XP, niveles, insignias, retos.  
10. **Videoclases** — reuniones con enlaces Meet/Zoom/Teams (manual + fallback).  
11. **Integraciones** — SMTP, calendario, webhooks, preparación pagos/WhatsApp/push.  
12. **DevOps** — salud del sistema, respaldos, colas, scheduler.

## Alcance

- **Incluido:** gestión académica-administrativa completa en intranet; portales docente/estudiante; sitio público; pruebas automatizadas (Feature, BDD, Cypress); documentación técnica y de esta carpeta.  
- **Preparado para futuro:** cobro en línea real (Mercado Pago/Culqi), WhatsApp Business API, push Firebase, OAuth Google Calendar completo, portal apoderado ampliado.

## Tecnologías usadas

| Capa | Tecnología |
|------|------------|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Inertia.js, React, TypeScript |
| Estilos | Tailwind CSS |
| Base de datos | MySQL (SQLite en desarrollo/tests) |
| Auth / roles | Laravel Breeze, Spatie Permission |
| Colas / caché | Database / Redis (producción recomendada) |
| IA | OpenAI (desacoplado; `NullAIProvider` si está deshabilitado) |
| Pruebas | PHPUnit, Gherkin (BDD), Cypress |
| Calidad | Laravel Pint, GitHub Actions CI |
| Contenedores | Docker / docker-compose (opcional) |

## Beneficios institucionales

- **Eficiencia administrativa** — un solo sistema para matrícula, cobranza y reportes.  
- **Mejora pedagógica** — LMS, diagnósticos y IA como apoyo al docente, no sustituto del criterio profesional.  
- **Transparencia** — auditoría, roles y trazabilidad de acciones sensibles.  
- **Imagen institucional** — sitio web premium y comunicación unificada.  
- **Escalabilidad** — arquitectura modular por fases y proveedores intercambiables.  
- **Calidad de software** — suite de pruebas y documentación para evaluación académica o auditoría técnica.
