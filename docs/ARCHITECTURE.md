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
| `Pages/Public` | Web institucional: `Public/Home`, `Public/Nosotros`, etc. Rutas en `PublicSiteController`; layout `PublicLayout`; componentes en `Components/Public/`. |
| `Pages/Intranet` | Área autenticada (dashboard, módulos operativos). |
| `Pages/Auth`, `Pages/Profile` | Breeze; el perfil usa el layout de intranet (`IntranetLayout`) para coherencia con el área autenticada. |
| `Layouts` | `PublicLayout` (web pública: navbar + footer). `IntranetLayout` (intranet). `GuestLayout` / `AuthenticatedLayout` (Breeze). |
| `Components` | `Components/Public/` (navbar, secciones landing, footer). `Components/Intranet/` (shell y widgets intranet). |
| `types` | Tipos compartidos TypeScript (`User`, `PageProps`, props por página). |

## Convenciones de nombres

- **PHP**: clases `PascalCase`, acciones `CreateFooAction`, servicios `FooService`, requests `StoreFooRequest`.
- **React**: componentes `PascalCase`, archivos de página alineados con la ruta Inertia.
- **Tests**: reflejar la ruta del código bajo prueba cuando sea posible (`tests/Feature/Intranet/...`).

## Autorización (Fase 2)

- **Roles** (`App\Enums\IntranetRole` + `spatie/laravel-permission`): Administrador, Secretaria, Docente, Estudiante, Apoderado.
- Rutas de intranet: middleware `auth`, `verified` y `role:` con la lista de roles del enum.
- **UserPolicy**: el usuario solo actualiza / elimina su propia cuenta.
- Detalle operativo: `docs/AUTHORIZATION.md`.

## Módulo de estudiantes (Fase 5)

- **Dominio**: modelo `App\Models\Student`, enums (`EducationalLevel`, `StudentStatus`, `Gender`, `DocumentType`), catálogo de grados `App\Support\StudentGradeCatalog`.
- **Entrega HTTP**: `StudentController` (delgado), validación en `StoreStudentRequest` / `UpdateStudentRequest`, reglas compartidas en `Http/Requests/Intranet/Concerns/ValidatesStudentAttributes`.
- **Servicio**: `App\Services\StudentService` (listados filtrados, alta y actualización con normalización de campos opcionales).
- **Autorización**: `StudentPolicy` + middleware `role:` por ruta (Administrador/Secretaria/Docente para consulta; Administrador/Secretaria para alta/edición); Estudiante y Apoderado excluidos del módulo.
- **Frontend**: páginas Inertia `Pages/Intranet/Students/*`, navegación habilitada en `App\Support\IntranetNavigation` solo para roles con acceso.

## Módulo de apoderados (Fase 6)

- **Dominio**: `App\Models\Guardian`, pivote `guardian_student` (N:N con `Student`), enum `GuardianRelationshipType` (parentesco en ficha y por vínculo), bandera `is_emergency_contact` en apoderado.
- **Entrega HTTP**: `GuardianController`, `StoreGuardianRequest` / `UpdateGuardianRequest` (incl. arreglo `students` con datos de pivote; filas vacías se descartan en `prepareForValidation`).
- **Servicio**: `App\Services\GuardianService` (sync de pivote, normalización, exclusividad de **responsable económico** y **contacto principal** por estudiante respecto a otros apoderados).
- **Autorización**: `GuardianPolicy` + middleware `role:` (misma matriz que estudiantes: consulta Administrador/Secretaria/Docente; escritura Administrador/Secretaria; **Estudiante** y **Apoderado** sin acceso al módulo).
- **Frontend**: `Pages/Intranet/Guardians/*`, `GuardianFormFields`, `GuardianStudentLinksEditor` (vincular estudiantes existentes con prioridad y roles en el vínculo).

## Estructura académica institucional (Fase 7)

- **Dominio**: `EducationalLevel` → `Grade` → `Section` → `Classroom` (`section_id` opcional en aula).
- **Entrega HTTP**: controladores `App\Http\Controllers\Academic\*`, Form Requests `Store*/Update*` por recurso, servicios `EducationalLevelService`, `GradeService`, `SectionService`, `ClassroomService`.
- **Autorización**: políticas por modelo; rutas de índice/detalle bajo `role:Administrador|Secretaria|Docente`; rutas de alta/edición/baja solo `role:Administrador` (coherente con matriz pedida).
- **Frontend**: páginas `Pages/Intranet/Academic/**`; menú **Gestión académica** con hijos en `IntranetNavigation` y render anidado en `Sidebar`; migas `Components/Intranet/IntranetBreadcrumbs`.

## Matrículas y año académico (Fase 8)

- **Dominio**: `AcademicYear` (año calendario único; solo uno `is_active`); `Enrollment` vinculado a estudiante, apoderado opcional, año académico, nivel y grados/sección del modelo académico (`EducationalLevel`, `Grade`, `Section`, `Classroom`), estado (`EnrollmentStatus`), monto y observaciones.
- **Entrega HTTP**: `EnrollmentController`, `AcademicYearController`; validación en `StoreEnrollmentRequest`, `UpdateEnrollmentRequest`, `StoreAcademicYearRequest`, `UpdateAcademicYearRequest`.
- **Servicios**: `EnrollmentService` (filtros en índice, generación de código, reglas de negocio); `AcademicYearService` (desactivar otros años al activar uno).
- **Soporte**: `EnrollmentFormCatalog` centraliza datos para selects dependientes en Inertia.
- **Autorización**: `EnrollmentPolicy`, `AcademicYearPolicy` + middleware `role:` (Docente solo lectura en matrículas; escritura Administrador/Secretaria; años académicos solo Administrador/Secretaria).
- **Frontend**: `Pages/Intranet/Enrollments/*`, `Components/Intranet/EnrollmentFormFields`; años académicos en `Pages/Intranet/AcademicYears/*`; entrada **Matrículas** en `IntranetNavigation`.

## Finanzas — conceptos, pensiones y pagos (Fase 9)

- **Dominio**: `PaymentConcept` (tipo y monto referencial); `Pension` por matrícula y periodo (mes/año únicos); `Payment` con método y estado de registro/anulación.
- **Entrega HTTP**: `PaymentConceptController`, `PensionController`, `PaymentController`; Form Requests `Store*/Update*`; generación de listados y pagos mediante `PaymentConceptService`, `PensionService`, `PaymentService` (transacciones, saldo pendiente, refresco de estado de pensión).
- **Autorización**: políticas dedicadas; rutas financieras solo `role:Administrador|Secretaria` (Docente, Estudiante y Apoderado sin acceso al menú **Finanzas**).
- **Frontend**: `Pages/Intranet/PaymentConcepts/*`, `Pages/Intranet/Pensions/*`, `Pages/Intranet/Payments/*`; submenú **Finanzas** en `IntranetNavigation` (mismo patrón que Gestión académica).
- **Fuera de esta fase**: boleta térmica final, PDF de comprobante, inventario y ventas.

## Qué queda fuera de fases tempranas

- Boleta térmica y PDF finales, inventario y ventas (roadmap); permisos más granulares por permiso Spatie si se requiere más allá de roles en rutas.

## Referencias

- Requerimientos generales: `SYSTEM_REQUIREMENTS.md`
- Plan por fases: `ROADMAP.md`
- Trazabilidad ISO: `docs/ISO_TRACEABILITY.md`
