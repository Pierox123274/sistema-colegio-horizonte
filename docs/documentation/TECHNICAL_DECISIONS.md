# Registro de decisiones técnicas (ADR consolidado)

Documento que explica las decisiones arquitectónicas del **Sistema Colegio Horizonte**, con ventajas, tradeoffs e impacto técnico. Orientado a evaluación académica y auditoría de diseño.

---

## 1. Laravel como framework backend

**Decisión:** PHP 8.2+ con Laravel 12.

| Ventajas | Tradeoffs | Impacto |
|----------|-----------|---------|
| Ecosistema maduro (auth, colas, migraciones, Eloquent) | Curva para equipos solo frontend | Time-to-market alto en ERP |
| Convenciones claras (MVC + servicios) | Hosting PHP tradicional vs serverless | Facilita mantenimiento institucional |
| Integración nativa con MySQL y testing | — | ~95 controladores, 43 migraciones |

---

## 2. React + Inertia.js

**Decisión:** SPA-like sin API REST separada para cada pantalla; Inertia entrega props desde controladores Laravel.

| Ventajas | Tradeoffs | Impacto |
|----------|-----------|---------|
| Un solo despliegue y sesión web | No es API-first para app móvil nativa sin capa adicional | ~220 páginas React |
| TypeScript en UI | Acoplamiento ruta PHP ↔ componente TSX | Tipado en formularios complejos |
| Menos duplicación de validación | — | Breeze + form requests en backend |

---

## 3. Arquitectura modular por dominios

**Decisión:** Separación lógica ERP, LMS, CMS, IA, Integraciones en carpetas `app/Services`, `app/Models/Cms`, `app/AI`, `app/Integrations`.

| Ventajas | Tradeoffs | Impacto |
|----------|-----------|---------|
| Equipos pueden enfocarse por módulo | Riesgo de dependencias cruzadas si no se usan servicios | ~60+ clases de servicio |
| Alineación con fases del roadmap | No microservicios — monolito modular | Despliegue único más simple para colegio |

---

## 4. Providers e interfaces (IA e integraciones)

**Decisión:** `AIProviderInterface`, `PaymentGatewayInterface`, `CalendarProviderInterface`, etc., con implementaciones concretas y **Null** fallback.

| Ventajas | Tradeoffs | Impacto |
|----------|-----------|---------|
| Cambio de proveedor sin reescribir negocio | Más archivos e indirección | Demos estables sin API keys |
| Tests con mocks/null | Diseño inicial más lento | `ExternalIntegrationsTest` verifica contratos |

---

## 5. Auditoría transversal

**Decisión:** `AuditService` + tabla `audit_logs` con `metadata` JSON (no columna `context`).

| Ventajas | Tradeoffs | Impacto |
|----------|-----------|---------|
| Trazabilidad para seguridad y IA | Volumen de datos en producción | Panel `/intranet/security/audit-logs` |
| Evidencia para compliance escolar | Necesita política de retención | Analítica IA lee `metadata` |

---

## 6. Aprendizaje adaptativo

**Decisión:** Módulo dedicado con banco de preguntas, intentos diagnósticos y recomendaciones (`AdaptiveDiagnosticService`).

| Ventajas | Tradeoffs | Impacto |
|----------|-----------|---------|
| Personalización pedagógica medible | Calidad depende del banco de ítems | Integración con LMS vía servicio puente |
| Datos para analytics adaptativo | No reemplaza evaluación oficial | Tablas `diagnostic_*`, `learning_recommendations` |

---

## 7. IA desacoplada del core ERP

**Decisión:** Paquete lógico `app/AI/` y servicios `AITutorService`, `TeacherAICopilotService` consumen proveedor por configuración.

| Ventajas | Tradeoffs | Impacto |
|----------|-----------|---------|
| Fallo de IA no tumba matrículas/pagos | Costos variables por tokens | Throttle `ai` en rutas sensibles |
| Ollama local para desarrollo | Latencia en generación larga | `NullAIProvider` para CI/demo |

---

## 8. Fallback providers

**Decisión:** `NullAIProvider`, `NullPaymentGateway`, `NullCalendarProvider`, `NullMessagingProvider`, `NullPushProvider`.

| Ventajas | Tradeoffs | Impacto |
|----------|-----------|---------|
| CI/CD y demos sin secretos | Funcionalidad “vacía” si no se configura | Mensajes claros al usuario |
| Contratos siempre implementados | Puede ocultar falta de config en prod | Checklist producción obligatorio |

---

## 9. Gamificación

**Decisión:** Capa motivacional (`GamificationService`) separada de calificaciones formales.

| Ventajas | Tradeoffs | Impacto |
|----------|-----------|---------|
| Engagement estudiantil | Riesgo de percibirse como “juego” si mal comunicado | XP no sustituye nota oficial |
| Eventos desde LMS | Reglas de negocio adicionales | Migración `gamification_tables` |

---

## 10. Queue y scheduler

**Decisión:** Jobs para notificaciones, recordatorios, escaneos de seguridad; `schedule` en `routes/console.php`.

| Ventajas | Tradeoffs | Impacto |
|----------|-----------|---------|
| Respuesta HTTP rápida | Requiere worker en producción | `QUEUE_CONNECTION` en .env |
| Tareas recurrentes institucionales | Supervisión de fallos en cola | Documentado en deployment guides |

---

## 11. Docker

**Decisión:** `docker-compose.yml` y `docker-compose.prod.yml` para entorno reproducible.

| Ventajas | Tradeoffs | Impacto |
|----------|-----------|---------|
| Onboarding de jurado/desarrolladores | Overhead en Windows sin WSL | PHP, MySQL, Node en contenedores |
| Paridad dev/prod aproximada | No sustituye hardening de servidor | Ver `DEVOPS.md` raíz |

---

## 12. Testing integral

**Decisión:** Pirámide PHPUnit Feature (336 tests) + Behat BDD (24 features) + Cypress E2E (24 specs).

| Ventajas | Tradeoffs | Impacto |
|----------|-----------|---------|
| Regresión en módulos críticos | Tiempo de pipeline | Confianza en fases 27–29 |
| Lenguaje de negocio en .feature | Mantenimiento dual (PHP + TS) | `npm run e2e` en CI opcional |

---

## Síntesis

El proyecto prioriza un **monolito modular bien probado** sobre microservicios, con **extensibilidad por interfaces** en IA e integraciones, y **operabilidad escolar** (auditoría, roles, backups) como requisitos no negociables.
