# Documentación técnica de pruebas — Sistema Colegio Horizonte

**Proyecto:** Sistema Colegio Horizonte  
**Stack:** Laravel 12 · PHP 8.2 · Inertia · React · TypeScript · MySQL  
**Estándar de referencia:** ISO/IEC 29119 (estrategia de pruebas)  
**Última actualización:** junio 2026

---

## 1. Resumen ejecutivo

El sistema cuenta con una **pirámide de pruebas** en cuatro capas complementarias:

| Capa | Tecnología | Archivos | Rol |
|------|------------|----------|-----|
| Unitarias + integración HTTP | **PHPUnit** (TDD) | 43 clases PHP | Regresión automatizada, políticas, persistencia |
| Especificación de negocio | **BDD / Gherkin** | 24 features | Trazabilidad requisito → escenario legible |
| End-to-end navegador | **Cypress** | 24 specs | Smoke de flujos reales con assets compilados |
| Calidad estática | **SonarQube** | 1 proyecto | Bugs, vulnerabilidades, deuda técnica, smells |
| Entorno reproducible | **Docker** | 3 manifests | Desarrollo, producción local y despliegue |

**Métricas recientes (suite local):**

- **340 tests PHPUnit** en verde (~1.732 aserciones)
- **0 bugs** y **0 críticos** en SonarQube tras último análisis
- **24 escenarios BDD** documentados en español
- **24 suites Cypress** para flujos críticos

---

## 2. Arquitectura de la estrategia de pruebas

```
                    ┌─────────────────────┐
                    │   SonarQube (SAST)  │  ← Calidad de código (PHP + TS)
                    └──────────┬──────────┘
                               │
         ┌─────────────────────┼─────────────────────┐
         │                     │                     │
         ▼                     ▼                     ▼
  ┌─────────────┐      ┌─────────────┐      ┌─────────────┐
  │  Cypress    │      │  PHPUnit    │      │  BDD        │
  │  (E2E)      │      │  Feature    │      │  Gherkin    │
  │  24 specs   │      │  39 clases  │      │  24 .feature│
  └──────┬──────┘      └──────┬──────┘      └──────┬──────┘
         │                    │                    │
         │              ┌─────┴─────┐                │
         │              │ PHPUnit   │                │
         │              │ Unit (4)  │                │
         │              └───────────┘                │
         │                    │                      │
         └────────────────────┴──────────────────────┘
                              │
                    ┌─────────▼─────────┐
                    │  Docker / CI      │
                    │  Entorno aislado  │
                    └───────────────────┘
```

### Principios aplicados

1. **TDD (Test-Driven Development):** las pruebas Feature y Unit validan comportamiento antes y después de cada cambio; la suite es la red de seguridad del refactor.
2. **BDD (Behavior-Driven Development):** los archivos `.feature` expresan comportamiento en lenguaje de negocio (español) y se vinculan explícitamente a tests PHPUnit automatizados.
3. **E2E selectivo:** Cypress cubre flujos de usuario completos sin duplicar toda la suite Feature.
4. **Análisis estático continuo:** SonarQube complementa las pruebas dinámicas detectando code smells, complejidad cognitiva y vulnerabilidades.
5. **Paridad de entornos:** Docker y CI usan las mismas versiones de PHP 8.2 y Node 20 que producción.

---

## 3. PHPUnit — TDD (pruebas unitarias e integración)

### 3.1 Configuración

| Archivo | Propósito |
|---------|-----------|
| `phpunit.xml` | Suites Unit + Feature, entorno `testing`, SQLite en memoria |
| `tests/TestCase.php` | Clase base de tests |
| `composer.json` | `phpunit/phpunit ^11.5`, `laravel/pint ^1.24` |

**Entorno de prueba (`phpunit.xml`):**

```xml
<env name="APP_ENV" value="testing"/>
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
<env name="CACHE_STORE" value="array"/>
<env name="QUEUE_CONNECTION" value="sync"/>
<env name="SESSION_DRIVER" value="array"/>
```

Cada test Feature con `RefreshDatabase` obtiene una base limpia sin depender de MySQL local.

### 3.2 Suites

#### Unit (`tests/Unit/` — 4 archivos)

Prueban lógica aislada sin HTTP:

| Archivo | Qué valida |
|---------|------------|
| `Unit/ExampleTest.php` | Smoke de configuración |
| `Unit/AI/LocalTutorFallbackServiceTest.php` | Respuestas locales del tutor IA sin API externa |
| `Unit/AI/OpenAIProviderTest.php` | Contrato del proveedor OpenAI (mocks HTTP) |
| `Unit/Support/SensitiveDataEncryptionTest.php` | Hash de documentos y detección de valores cifrados |

#### Feature (`tests/Feature/` — 39 archivos)

Prueban rutas HTTP, políticas de autorización, Inertia, persistencia y servicios:

| Módulo | Archivo(s) | Escenarios clave |
|--------|------------|------------------|
| **Auth** | `Auth/AuthenticationTest.php`, `RegistrationTest.php`, `PasswordResetTest.php`, etc. | Login, logout, registro, verificación email |
| **Intranet — estudiantes** | `Intranet/StudentManagementTest.php` | CRUD, roles, unicidad código/documento |
| **Intranet — apoderados** | `Intranet/GuardianManagementTest.php` | Alta, vínculos, validaciones |
| **Intranet — matrículas** | `Intranet/EnrollmentManagementTest.php` | Alta, edición, búsqueda estudiante |
| **Intranet — académico** | `AcademicStructureTest.php`, `AcademicGradesManagementTest.php` | Niveles, grados, notas |
| **Intranet — asistencia** | `AttendanceManagementTest.php` | Registro masivo, historial |
| **Intranet — finanzas** | `FinanceManagementTest.php`, `PaymentReceiptTest.php` | Pagos, pensiones, recibos |
| **Intranet — inventario/ventas** | `InventoryManagementTest.php`, `CashSalesManagementTest.php` | Stock, caja, ventas |
| **Intranet — analítica** | `AnalyticsDashboardTest.php` | Dashboard ejecutivo |
| **Intranet — admin** | `AdminUserAndTeacherAssignmentTest.php` | Usuarios, asignaciones docentes |
| **Portal docente** | `Teacher/TeacherPortalTest.php` | Dashboard, asignaciones, asistencia |
| **Portal estudiante** | `Student/StudentPortalTest.php` | Dashboard, perfil, portal scoped |
| **LMS** | `LMS/VirtualClassroomTest.php` | Aulas, tareas, recursos |
| **Aprendizaje adaptativo** | `AdaptiveLearning/AdaptiveLearningTest.php` | Exámenes diagnóstico, banco preguntas |
| **IA** | `AI/AITutorTest.php`, `AdvancedAIFeaturesTest.php` | Tutor, copiloto docente, fallbacks |
| **CMS** | `CMS/CmsManagementTest.php` | Páginas, noticias, configuración sitio |
| **Seguridad** | `Security/AuditSecurityTest.php` | Auditoría, sesiones, intentos fallidos |
| **Notificaciones** | `Notifications/NotificationSystemTest.php` | Bandeja, preferencias |
| **Videoclases** | `Meetings/VirtualMeetingsTest.php` | Reuniones, participantes |
| **Integraciones** | `Integrations/ExternalIntegrationsTest.php` | Panel integraciones, webhooks |
| **Gamificación** | `Gamification/GamificationTest.php` | XP, logros, rankings |
| **Sistema / DevOps** | `System/DevOpsInfrastructureTest.php`, `ProductionReadinessTest.php` | Salud, backups, colas |
| **QA integral** | `System/PlatformQualityAssuranceTest.php` | Rutas protegidas, navegación, portales |
| **Público** | `Public/PublicSiteTest.php` | Homepage, CMS público |
| **Comunicados** | `Announcements/AnnouncementManagementTest.php` | CRUD comunicados |

### 3.3 Patrones de aserción usados

```php
// HTTP + Inertia
$response->assertOk();
$response->assertInertia(fn (Assert $page) => $page->component('Intranet/Students/Index'));

// Autorización
$response->assertForbidden();

// Persistencia
$this->assertDatabaseHas('students', ['code' => 'EST-100001']);

// Datos cifrados (documento)
$this->assertSame('45123456', Guardian::query()->value('document_number'));
$this->assertDatabaseHas('guardians', [
    'document_number_hash' => SensitiveDataHasher::hashDocument('45123456'),
]);
```

### 3.4 Cobertura de código

`phpunit.xml` genera reportes en:

- `build/coverage/clover.xml` — consumido por SonarQube
- `build/coverage/html/` — reporte HTML local

```bash
# Generar cobertura (requiere Xdebug o PCOV)
php artisan test --coverage
# o
vendor/bin/phpunit --coverage-clover build/coverage/clover.xml
```

> **Nota:** en entornos sin extensión de cobertura, los tests ejecutan igual; SonarQube reportará 0 % hasta generar el XML.

### 3.5 Comandos TDD

```bash
# Suite completa
php artisan test

# Por carpeta o filtro
php artisan test tests/Feature/AI/
php artisan test --filter=StudentManagementTest

# Estilo PHP (Laravel Pint)
vendor/bin/pint --dirty
vendor/bin/pint --test          # solo verificar, sin modificar
```

---

## 4. BDD — Behavior-Driven Development (Gherkin)

### 4.1 Ubicación y formato

```
tests/Bdd/features/
├── authentication.feature
├── students.feature
├── guardians.feature
├── enrollments.feature
├── payments.feature
├── payment_receipts.feature
├── academic_structure.feature
├── academic_grades.feature
├── attendance.feature
├── inventory.feature
├── virtual_classroom.feature
├── adaptive_learning.feature
├── ai_tutor.feature
├── advanced_ai.feature
├── teacher_portal.feature
├── gamification.feature
├── cms_management.feature
├── notifications.feature
├── virtual_meetings.feature
├── analytics_dashboard.feature
├── security_audit.feature
├── external_integrations.feature
├── devops_infrastructure.feature
└── platform_quality_assurance.feature
```

**Total: 24 archivos `.feature`** en español (`# language: es`).

### 4.2 Ejemplo de especificación

```gherkin
# tests/Bdd/features/students.feature
Característica: Gestión de estudiantes en la intranet
  Como personal autorizado
  Quiero registrar y consultar fichas de estudiantes
  Para mantener el padrón institucional con control de acceso

  Escenario: Secretaría registra un nuevo estudiante
    Dado que inicio sesión como Secretaría
    Cuando envío el formulario de alta con datos válidos y únicos
    Entonces el estudiante queda guardado y puedo ver su ficha
```

### 4.3 Relación BDD → automatización

Los features **no usan Behat** como runner independiente. Cada archivo `.feature` declara en comentario su **test Feature PHPUnit equivalente**:

```gherkin
# Cobertura automatizada: ver tests/Feature/Intranet/StudentManagementTest.php
```

| Feature BDD | Test PHPUnit automatizado |
|-------------|---------------------------|
| `authentication.feature` | `tests/Feature/Auth/AuthenticationTest.php` |
| `students.feature` | `tests/Feature/Intranet/StudentManagementTest.php` |
| `guardians.feature` | `tests/Feature/Intranet/GuardianManagementTest.php` |
| `enrollments.feature` | `tests/Feature/Intranet/EnrollmentManagementTest.php` |
| `payments.feature` | `tests/Feature/Intranet/FinanceManagementTest.php` |
| `attendance.feature` | `tests/Feature/Intranet/AttendanceManagementTest.php` |
| `virtual_classroom.feature` | `tests/Feature/LMS/VirtualClassroomTest.php` |
| `ai_tutor.feature` | `tests/Feature/AI/AITutorTest.php` |
| `security_audit.feature` | `tests/Feature/Security/AuditSecurityTest.php` |
| `platform_quality_assurance.feature` | `tests/Feature/System/PlatformQualityAssuranceTest.php` |
| `devops_infrastructure.feature` | `tests/Feature/System/DevOpsInfrastructureTest.php` |
| *(resto de features)* | Clase homónima en `tests/Feature/` |

### 4.4 Propósito del BDD en este proyecto

- **Trazabilidad ISO 29119:** requisito → escenario → test automatizado.
- **Comunicación con stakeholders** no técnicos (docentes, dirección).
- **Criterios de aceptación** explícitos por módulo.

---

## 5. Cypress — pruebas end-to-end (E2E)

### 5.1 Configuración

| Archivo | Contenido |
|---------|-----------|
| `cypress.config.ts` | `baseUrl`, patrón de specs, sin video |
| `cypress/support/e2e.ts` | Hooks globales |
| `package.json` | `"e2e": "cypress run"`, `"e2e:open": "cypress open"` |

```typescript
// cypress.config.ts
export default defineConfig({
    e2e: {
        baseUrl: process.env.CYPRESS_BASE_URL ?? 'http://localhost:8000',
        specPattern: 'cypress/e2e/**/*.cy.ts',
        video: false,
    },
});
```

### 5.2 Suites E2E (24 archivos)

| Spec | Flujo validado |
|------|----------------|
| `auth.cy.ts` | Pantalla login, redirección invitados |
| `students.cy.ts` | Listado y gestión estudiantes |
| `guardians.cy.ts` | Apoderados |
| `enrollments.cy.ts` | Matrículas |
| `payments.cy.ts` | Pagos y finanzas |
| `payment-receipts.cy.ts` | Comprobantes |
| `academic-structure.cy.ts` | Niveles, grados, secciones |
| `academic-grades.cy.ts` | Registro de notas |
| `attendance.cy.ts` | Asistencia |
| `inventory.cy.ts` | Inventario y stock |
| `teacher-portal.cy.ts` | Portal docente |
| `virtual-classroom.cy.ts` | LMS / aula virtual |
| `adaptive-learning.cy.ts` | Diagnósticos adaptativos |
| `ai-tutor.cy.ts` | Tutor IA estudiante |
| `advanced-ai.cy.ts` | IA institucional / copiloto |
| `cms-management.cy.ts` | CMS intranet |
| `notifications.cy.ts` | Notificaciones |
| `virtual-meetings.cy.ts` | Videoclases |
| `analytics-dashboard.cy.ts` | Dashboard analítico |
| `security-audit.cy.ts` | Auditoría y seguridad |
| `external-integrations.cy.ts` | Panel integraciones |
| `gamification.cy.ts` | Gamificación |
| `devops-infrastructure.cy.ts` | Salud del sistema |
| `platform-quality-assurance.cy.ts` | QA transversal |

### 5.3 Ejemplo de test

```typescript
// cypress/e2e/auth.cy.ts
describe('Autenticación (base)', () => {
    it('muestra la pantalla de login', () => {
        cy.visit('/login');
        cy.contains('Log in').should('exist');
    });

    it('redirige invitados desde la intranet al login', () => {
        cy.visit('/intranet/dashboard');
        cy.url().should('include', '/login');
    });
});
```

### 5.4 Prerrequisitos y ejecución

```bash
# 1. Compilar frontend (obligatorio para Inertia/Vite)
npm run build

# 2. Servidor Laravel en otra terminal
php artisan serve

# 3. Ejecutar E2E
npm run e2e

# UI interactiva
npm run e2e:open

# URL personalizada
CYPRESS_BASE_URL=http://localhost:8080 npm run e2e
```

**Variables:** `CYPRESS_BASE_URL` (documentada en `.env.example`).

### 5.5 Qué valida Cypress que PHPUnit no cubre

- Renderizado real de React/Inertia en navegador.
- Assets Vite compilados (`public/build`).
- Navegación, formularios y redirecciones desde la perspectiva del usuario.
- Regresiones visuales básicas (elementos visibles en DOM).

---

## 6. SonarQube — análisis estático de calidad

### 6.1 Configuración del proyecto

Archivo: `sonar-project.properties`

```properties
sonar.projectKey=sistema-colegio-horizonte
sonar.projectName=Sistema Colegio Horizonte
sonar.sources=app,routes,database,resources/js,bootstrap,config
sonar.tests=tests
sonar.exclusions=**/vendor/**,**/node_modules/**,**/public/build/**,**/storage/**
sonar.php.coverage.reportPaths=build/coverage/clover.xml
```

**Lenguajes analizados:** PHP, TypeScript/TSX, JavaScript.

### 6.2 Ejecución local (Docker)

```bash
# 1. Levantar SonarQube Community
docker run -d --name sonarqube -p 9000:9000 sonarqube:community

# 2. Generar token en http://localhost:9000 (admin/admin por defecto)

# 3. (Opcional) Generar cobertura PHPUnit
vendor/bin/phpunit --coverage-clover build/coverage/clover.xml

# 4. Escanear
docker run --rm \
  -e SONAR_HOST_URL=http://host.docker.internal:9000 \
  -e SONAR_TOKEN=<token> \
  -v "%cd%:/usr/src" \
  sonarsource/sonar-scanner-cli
```

Dashboard: `http://localhost:9000/dashboard?id=sistema-colegio-horizonte`

### 6.3 Métricas y reglas relevantes

| Métrica | Descripción |
|---------|-------------|
| **Bugs** | Errores probables en tiempo de ejecución |
| **Vulnerabilities** | Fallos de seguridad (OWASP) |
| **Code Smells** | Mantenibilidad, duplicación, complejidad |
| **Cognitive Complexity (S3776)** | Funciones con demasiada lógica anidada |
| **Coverage** | % líneas cubiertas por PHPUnit (si hay clover.xml) |
| **Duplications** | Código duplicado entre archivos |

### 6.4 Resultados del último ciclo de mejora

Tras refactors orientados a SonarQube:

| Métrica | Inicial (aprox.) | Final |
|---------|------------------|-------|
| Críticos | 50 | **0** |
| Bugs | 5 | **0** |
| Code smells | ~830 | ~784 |
| LOC analizadas | ~76.500 | ~76.500 |

**Correcciones destacadas:**

- Refactor de complejidad cognitiva en servicios PHP (`SystemHealthService`, `IntranetNavigation`, FormRequests).
- Extracción de componentes/hooks React (`AITutor`, `Sidebar`, `EnrollmentFormFields`).
- Fix de accesibilidad (teclado en `Dropdown`, `MobileNavMenu`).
- Configuración de cobertura PHPUnit → SonarQube.

### 6.5 Integración recomendada en CI

SonarQube no está en `.github/workflows/ci.yml` actual. Para integrarlo:

1. Añadir paso `phpunit --coverage-clover`.
2. Ejecutar `sonarsource/sonar-scanner-cli` con `SONAR_TOKEN` en secrets.
3. Opcional: SonarCloud para análisis en la nube sin Docker local.

---

## 7. Docker — entornos de prueba y despliegue

### 7.1 Desarrollo (`docker-compose.yml`)

| Servicio | Imagen / build | Puerto | Función |
|----------|----------------|--------|---------|
| `app` | `docker/Dockerfile.dev` | — | PHP-FPM, código montado |
| `nginx` | nginx:1.27-alpine | **8080** | Proxy HTTP |
| `mysql` | mysql:8.4 | **33060** | Base de datos |
| `redis` | redis:7-alpine | **63790** | Cache, colas, sesiones |
| `queue` | mismo build app | — | `queue:work` |
| `scheduler` | mismo build app | — | `schedule:work` |

```bash
docker compose up -d
# App: http://localhost:8080
```

### 7.2 Producción local (`docker-compose.prod.yml`)

Igual que desarrollo con:

- `APP_ENV=production`, `APP_DEBUG=false`
- `SESSION_SECURE_COOKIE=true`
- Healthchecks en app, mysql y redis
- Volúmenes persistentes `mysql_data_prod`, `redis_data_prod`
- Nginx en puerto **80** con `docker/nginx.prod.conf`

### 7.3 Imagen de producción (`Dockerfile`)

Multi-stage:

1. **Stage `assets`:** Node 20 → `npm ci && npm run build`
2. **Stage final:** PHP 8.2-cli → extensiones `pdo_mysql`, `gd`, `intl`, etc.
3. **CMD:** `docker/railway-start.sh`

Secuencia de arranque en producción:

```bash
php artisan migrate --force
php artisan data:encrypt-personal    # cifrado datos sensibles
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan serve --host=0.0.0.0 --port=$PORT
```

### 7.4 Railway (nube)

| Archivo | Rol |
|---------|-----|
| `railway.json` | Build con Railpack + `npm run build` |
| `railway/init-app.sh` | Pre-deploy |
| `docker/railway-start.sh` | Migraciones, seed, serve |

**Producción:** https://web-production-c87da.up.railway.app

### 7.5 Render (alternativa)

`render.yaml` — blueprint con plan gratuito, variables `DB_*` y `APP_KEY` sincronizadas manualmente.

---

## 8. CI/CD — GitHub Actions

Archivo: `.github/workflows/ci.yml`

**Disparadores:** push a `main`, `master`, `develop`; pull requests.

**Pipeline:**

```
checkout → PHP 8.2 → Node 20 → composer install
    → .env + key:generate → migrate (sqlite file)
    → pint --test → php artisan test
    → npm ci → npm run build
```

| Paso | Herramienta | Criterio de éxito |
|------|-------------|-------------------|
| Estilo PHP | Laravel Pint | Sin diffs de formato |
| Tests | PHPUnit | 340 tests PASS |
| Frontend | `tsc && vite build` | Build sin errores |

> **Nota:** el workflow puede requerir scope `workflow` en el token de GitHub para pushearse al remoto. Ejecutar localmente con los mismos comandos si CI no está activo en el repo.

---

## 9. Matriz de trazabilidad (módulo → BDD → PHPUnit → Cypress)

| Módulo | BDD Feature | PHPUnit Feature | Cypress E2E |
|--------|-------------|-----------------|-------------|
| Autenticación | `authentication.feature` | `Auth/*` | `auth.cy.ts` |
| Estudiantes | `students.feature` | `StudentManagementTest` | `students.cy.ts` |
| Apoderados | `guardians.feature` | `GuardianManagementTest` | `guardians.cy.ts` |
| Matrículas | `enrollments.feature` | `EnrollmentManagementTest` | `enrollments.cy.ts` |
| Pagos | `payments.feature` | `FinanceManagementTest` | `payments.cy.ts` |
| Comprobantes | `payment_receipts.feature` | `PaymentReceiptTest` | `payment-receipts.cy.ts` |
| Estructura académica | `academic_structure.feature` | `AcademicStructureTest` | `academic-structure.cy.ts` |
| Notas | `academic_grades.feature` | `AcademicGradesManagementTest` | `academic-grades.cy.ts` |
| Asistencia | `attendance.feature` | `AttendanceManagementTest` | `attendance.cy.ts` |
| Inventario | `inventory.feature` | `InventoryManagementTest` | `inventory.cy.ts` |
| Portal docente | `teacher_portal.feature` | `TeacherPortalTest` | `teacher-portal.cy.ts` |
| LMS | `virtual_classroom.feature` | `VirtualClassroomTest` | `virtual-classroom.cy.ts` |
| Adaptativo | `adaptive_learning.feature` | `AdaptiveLearningTest` | `adaptive-learning.cy.ts` |
| Tutor IA | `ai_tutor.feature` | `AITutorTest` | `ai-tutor.cy.ts` |
| IA avanzada | `advanced_ai.feature` | `AdvancedAIFeaturesTest` | `advanced-ai.cy.ts` |
| CMS | `cms_management.feature` | `CmsManagementTest` | `cms-management.cy.ts` |
| Notificaciones | `notifications.feature` | `NotificationSystemTest` | `notifications.cy.ts` |
| Videoclases | `virtual_meetings.feature` | `VirtualMeetingsTest` | `virtual-meetings.cy.ts` |
| Analítica | `analytics_dashboard.feature` | `AnalyticsDashboardTest` | `analytics-dashboard.cy.ts` |
| Seguridad | `security_audit.feature` | `AuditSecurityTest` | `security-audit.cy.ts` |
| Integraciones | `external_integrations.feature` | `ExternalIntegrationsTest` | `external-integrations.cy.ts` |
| Gamificación | `gamification.feature` | `GamificationTest` | `gamification.cy.ts` |
| DevOps | `devops_infrastructure.feature` | `DevOpsInfrastructureTest` | `devops-infrastructure.cy.ts` |
| QA integral | `platform_quality_assurance.feature` | `PlatformQualityAssuranceTest` | `platform-quality-assurance.cy.ts` |

---

## 10. Guía rápida de ejecución (checklist)

### Antes de un commit

```bash
vendor/bin/pint --dirty
php artisan test
npm run build
```

### Antes de un release / despliegue

```bash
php artisan test
npm run build
npm run e2e                    # con servidor corriendo
vendor/bin/phpunit --coverage-clover build/coverage/clover.xml
# SonarQube scan (opcional)
docker compose -f docker-compose.prod.yml up -d --build
```

### Credenciales de prueba (seed demo)

| Rol | Email | Contraseña |
|-----|-------|------------|
| Admin | `test@example.com` | `password` |
| Estudiante | `estudiante@demo.com` | `password` |

---

## 11. Limitaciones conocidas

| Área | Limitación | Mitigación |
|------|------------|------------|
| Cobertura SonarQube | 0 % sin PCOV/Xdebug local | Instalar extensión y generar `clover.xml` |
| BDD | Sin runner Behat; specs son documentación + enlace a PHPUnit | Mantener comentario de trazabilidad en cada `.feature` |
| Cypress | No corre en CI actual | Añadir job con `cypress-io/github-action` |
| SonarQube | Análisis manual local | Integrar SonarCloud en GitHub Actions |
| E2E | Requiere `npm run build` previo | Documentado en sección 5.4 |
| SQLite vs MySQL | Tests usan SQLite en memoria | `ProductionReadinessTest` valida configuración prod |

Ver también: `docs/KNOWN_LIMITATIONS.md`

---

## 12. Documentos relacionados

| Documento | Ruta |
|-----------|------|
| Visión general pruebas | `docs/documentation/testing/TEST_DOCUMENTATION.md` |
| Feature tests detalle | `docs/documentation/testing/FEATURE_TESTS_DOCUMENTATION.md` |
| BDD | `docs/documentation/testing/BDD_TESTS_DOCUMENTATION.md` |
| Cypress | `docs/documentation/testing/CYPRESS_TESTS_DOCUMENTATION.md` |
| Seguridad en tests | `docs/documentation/testing/SECURITY_TESTS_DOCUMENTATION.md` |
| IA en tests | `docs/documentation/testing/AI_TESTS_DOCUMENTATION.md` |
| QA | `docs/documentation/testing/QA_TESTS_DOCUMENTATION.md` |
| Despliegue | `docs/documentation/deployment/DEPLOYMENT_GUIDE_EXTENDED.md` |
| Seguridad producción | `docs/documentation/security/PRODUCTION_SECURITY_CHECKLIST.md` |
| SonarQube config | `sonar-project.properties` |
| CI workflow | `.github/workflows/ci.yml` |

---

## 13. Glosario

| Término | Significado en este proyecto |
|---------|------------------------------|
| **TDD** | Desarrollo guiado por tests; PHPUnit como red de regresión |
| **BDD** | Escenarios Gherkin que describen comportamiento de negocio |
| **Feature test** | Test PHPUnit que ejerce HTTP + base de datos |
| **Unit test** | Test PHPUnit de clase/método aislado |
| **E2E** | Test Cypress en navegador real |
| **SAST** | Análisis estático (SonarQube) sin ejecutar la app |
| **Smoke test** | Subconjunto mínimo E2E para verificar que el sistema arranca |
| **RefreshDatabase** | Trait Laravel que reinicia schema en cada test |
| **Inertia assert** | `assertInertia()` valida componente React devuelto |

---

*Documento generado para el repositorio `sistema-colegio-horizonte`. Mantener sincronizado al añadir módulos, features BDD, specs Cypress o cambios en la pipeline de calidad.*
