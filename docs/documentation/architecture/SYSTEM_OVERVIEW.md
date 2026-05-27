# Arquitectura — Visión general del sistema

## Propósito

Describir cómo se organizan las capas del **Sistema Colegio Horizonte** y cómo interactúan los portales (público, intranet, docente, estudiante).

## Componentes

| Capa | Ubicación | Responsabilidad |
|------|-----------|-----------------|
| Presentación | `resources/js/Pages`, `Components` | UI React/Inertia por rol |
| HTTP | `routes/web.php`, `routes/webhooks.php` | Rutas, middleware, políticas |
| Aplicación | `app/Http/Controllers`, `Services` | Casos de uso, orquestación |
| Dominio | `app/Models`, `Enums`, `Policies` | Entidades y reglas de acceso |
| Infraestructura | `app/AI`, `app/Meetings`, `app/Integrations` | Proveedores externos desacoplados |
| Persistencia | `database/migrations`, Eloquent | MySQL |
| Operaciones | `app/Jobs`, `routes/console.php` | Colas, scheduler, respaldos |

## Flujo típico de petición

1. Usuario accede por navegador (público o autenticado).  
2. Middleware: sesión, roles Spatie, cabeceras de seguridad, auditoría de actividad.  
3. Controlador autoriza vía Policy o `Gate` sobre dashboards/agregados.  
4. Servicio ejecuta lógica de negocio y transacciones DB.  
5. Respuesta Inertia (JSON + página React) o redirect/JSON API (IA, webhooks).

## Tecnologías

Laravel 12, Inertia, React, TypeScript, Tailwind, MySQL, OpenAI (opcional).

## Decisiones técnicas

- **Monolito modular** — un despliegue, módulos por carpetas y servicios; facilita instituciones medianas.  
- **Inertia** — SPA sin API REST duplicada para la mayoría de pantallas intranet.  
- **Proveedores intercambiables** — IA, reuniones, integraciones con interfaces + implementación Null.

## Relación con otros módulos

Ver documentos especializados: ERP, LMS, CMS, IA, seguridad, integraciones, despliegue y base de datos en esta misma carpeta `architecture/`.
