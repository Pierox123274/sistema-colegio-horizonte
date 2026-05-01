# Política de seguridad de la información (marco inicial)

Documento orientado a **ISO/IEC 27001** y a las exigencias del `SYSTEM_REQUIREMENTS.md` (protección de datos personales y académicos, accesos, auditoría). No sustituye una política corporativa firmada por la institución; define **criterios técnicos y de desarrollo** del repositorio.

## Alcance

- Sistema web institucional + intranet del I.E.P. Horizonte.
- Datos de estudiantes, apoderados, personal y finanzas tratados como **información sensible**.

## Roles y responsabilidades (desarrollo)

| Rol | Responsabilidad |
|-----|-----------------|
| Mantenedor del repositorio | Revisiones de código, dependencias, secretos y configuración de entorno. |
| Desarrolladores | Cumplir validación, autorización mínima, no exponer datos en logs ni respuestas de error. |

## Controles técnicos previstos (evolución del proyecto)

1. **Autenticación y sesión**: mecanismos Laravel/Sanctum según diseño aprobado en fases de auth; sesiones seguras en producción (HTTPS, cookies `secure`, `httpOnly` donde aplique).
2. **Autorización**: policies y middleware; principio de **mínimo privilegio** por rol (Administrador, Secretaría, Docente, Estudiante, Apoderado — ver requerimientos).
3. **Validación de entrada**: Form Requests en `app/Http/Requests`; sanitización según el vector (SQL vía Eloquent, XSS en vistas/React).
4. **Datos en tránsito y en reposo**: TLS en producción; credenciales y claves solo en variables de entorno (nunca en el código ni en el historial).
5. **Secretos**: `.env` fuera de control de versiones; rotación de claves documentada en despliegue.
6. **Auditoría**: registro de acciones críticas (fases posteriores) alineado con módulo de auditoría del sistema.
7. **Copias de seguridad y recuperación**: responsabilidad de operaciones/hosting; el código debe permitir migraciones reproducibles.

## Gestión de vulnerabilidades

- Actualizar `composer` y `npm` de forma planificada.
- Revisar advisories antes de releases a producción.

## Privacidad

- Minimizar datos en props de Inertia y en APIs.
- Cumplir finalidad educativa/administrativa informada a la institución (texto legal a cargo del colegio).

## Relación con otros documentos

- Calidad y pruebas: `docs/TESTING_STRATEGY.md`
- Trazabilidad normativa: `docs/ISO_TRACEABILITY.md`
- Arquitectura: `docs/ARCHITECTURE.md`
