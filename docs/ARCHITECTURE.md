# Arquitectura del sistema — I.E.P. Horizonte

Este documento fija la arquitectura base del proyecto **Laravel 12 + Inertia + React (TypeScript)**. Objetivo: mantenibilidad y escalabilidad sin **Clean Architecture pesada**: capas claras, pocas abstracciones, crecimiento por dominio cuando aparezca lógica real.

## Principios

1. **Laravel como capa de entrega**: HTTP, autenticación, colas, vistas Inertia y políticas siguen siendo el marco principal.
2. **Orquestación fina**: los controladores delegan en **Actions** (un caso de uso por clase o por método explícito) y en **Services** cuando el flujo crece o se reutiliza entre puntos de entrada.
3. **Modelos delgados**: Eloquent para persistencia y relaciones; reglas complejas o transacciones multi-modelo preferentemente fuera del modelo (Services/Actions).
4. **DTOs en los bordes**: objetos de transferencia para agrupar datos entre capas (respuestas a Inertia, integraciones externas) cuando un array suelto dificulta el mantenimiento.
5. **Frontend por contexto**: páginas públicas bajo `Pages/Public`, intranet bajo `Pages/Intranet`; componentes y layouts compartidos en `Components` y `Layouts`.

## Estructura backend (`app/`)

| Ruta | Uso |
|------|-----|
| `Http/Controllers` | Entrada HTTP; validación delegada a Form Requests; sin lógica de negocio voluminosa. |
| `Http/Requests` | Validación y autorización por formulario. Subcarpetas: `Auth` (Breeze), `Intranet`, `Public`. |
| `Actions` | Casos de uso puntuales: “crear X”, “importar Y”. Idealmente invocables desde controladores, jobs o comandos. |
| `Services` | Coordinación, reutilización entre acciones, integraciones (APIs, archivos). Evitar “god services”. |
| `DTOs` | Estructuras de datos inmutables o tipadas entre capas (no Eloquent). |
| `Enums` | Estados, tipos y catálogos cerrados (PHP 8.1+). |
| `Models` | Entidades Eloquent. |
| `Policies` / `Middleware` | Autorización y controles transversales (según se implemente). |
| `Support` | Utilidades puras sin estado de dominio (formateo, cálculos genéricos, helpers de librería). |
| `Traits` | Comportamiento reutilizable entre clases del dominio o infraestructura (con moderación). |

## Flujo recomendado (resumen)

```
Request → Form Request → Controller → Action y/o Service → Model / DB
                ↓
         Inertia::render(..., DTO/array estable)
```

Los **Jobs** y **Commands** pueden llamar a las mismas Actions/Services que los controladores.

## Frontend (`resources/js/`)

| Ruta | Uso |
|------|-----|
| `Pages/Public` | Web institucional (SEO, marketing, formularios públicos). Convención Inertia: `Public/Nombre` → `Inertia::render('Public/Nombre')`. |
| `Pages/Intranet` | Área autenticada (dashboard, módulos operativos). |
| `Pages/Auth`, `Pages/Profile` | Se mantienen como en Breeze hasta refactor explícito (no obligatorio en Fase 1). |
| `Layouts` | Shell visual: público vs autenticado cuando existan layouts definitivos. |
| `Components` | UI reutilizable (botones, formularios, tablas). Agrupar por subcarpetas solo cuando el volumen lo exija (`ui/`, `forms/`, etc.). |
| `types` | Tipos compartidos TypeScript (`User`, `PageProps`, props por página). |

## Convenciones de nombres

- **PHP**: clases `PascalCase`, acciones `CreateFooAction`, servicios `FooService`, requests `StoreFooRequest`.
- **React**: componentes `PascalCase`, archivos de página alineados con la ruta Inertia.
- **Tests**: reflejar la ruta del código bajo prueba cuando sea posible (`tests/Feature/Intranet/...`).

## Qué queda fuera de Fase 1

- CRUD de negocio, políticas por rol, módulos académicos/financieros.
- Cambios en pantallas de login o flujo Breeze salvo decisión explícita en fases posteriores.

## Referencias

- Requerimientos generales: `SYSTEM_REQUIREMENTS.md`
- Plan por fases: `ROADMAP.md`
- Trazabilidad ISO: `docs/ISO_TRACEABILITY.md`
