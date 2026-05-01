# Autorización y roles (Fase 2)

## Paquete

- **spatie/laravel-permission** (guard `web`): roles almacenados en BD, comprobación en middleware y con `$user->can()` cuando se añadan permisos.

## Roles iniciales

| Rol | Uso previsto |
|-----|----------------|
| Administrador | Configuración, usuarios, seguridad, auditoría. |
| Secretaria | Matrículas, pensiones, pagos, ventas básicas. |
| Docente | Cursos, evaluaciones, seguimiento. |
| Estudiante | Progreso propio, tutor (fases posteriores). |
| Apoderado | Pagos, comprobantes, seguimiento del menor. |

Los nombres deben coincidir exactamente con `App\Enums\IntranetRole` y con los registros creados por `RoleSeeder`.

## Rutas

- Área intranet (dashboard y perfil): middleware `auth`, `verified` y `role:` con la lista de roles del enum.
- Autoregistro (`register`): asigna por defecto el rol **Estudiante** (mínimo privilegio para entrar a intranet).

## Políticas

- `UserPolicy`: un usuario solo puede actualizar o eliminar **su propia** cuenta (perfil Breeze).

## Frontend

- Navegación lateral: `sidebarNav` generada en servidor vía `App\Support\IntranetNavigation` (evita mostrar enlaces no respaldados por permisos).
- Resumen contextual por rol en `Intranet/Dashboard`.

## Despliegue

Tras `migrate`, ejecutar al menos:

```bash
php artisan db:seed --class=Database\\Seeders\\RoleSeeder
```

Sin roles en BD, `assignRole` en el registro fallará.

## Middleware registrados

Alias en `bootstrap/app.php`: `role`, `permission`, `role_or_permission` (Spatie).
