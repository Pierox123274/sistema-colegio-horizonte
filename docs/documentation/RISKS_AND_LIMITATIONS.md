# Riesgos y limitaciones del sistema

Análisis honesto para evaluación académica y despliegue productivo. Separado en limitaciones actuales, riesgos técnicos y mitigaciones.

---

## 1. Limitaciones actuales

| Área | Limitación | Notas |
|------|------------|-------|
| **Pagos en línea** | Sin cobro automático en producción | Providers `MercadoPago`/`Culqi` preparados; flujo manual en secretaría |
| **WhatsApp** | Sin envío real | `NullMessagingProvider` por defecto |
| **Push móvil** | Sin FCM activo | `NullPushProvider` |
| **OAuth / SSO** | Solo login email/password Breeze | Google Calendar OAuth incompleto |
| **Videoconferencia** | URL manual (Meet/Zoom/Teams) | Sin creación de sala por API |
| **Portal apoderado** | Rol definido; portal limitado | Evolución en roadmap |
| **IA sin API key** | Respuestas vacías o mensaje instructivo | `NullAIProvider` |
| **Almacenamiento** | Disco local Laravel | Cloud S3/Azure **preparado** vía `ExternalStorageService` |
| **Multicolegio** | Instancia única | Sin `tenant_id` global |

---

## 2. Riesgos técnicos

| ID | Riesgo | Probabilidad | Impacto |
|----|--------|--------------|---------|
| R1 | **Cuota OpenAI** agotada o costos elevados | Media | Alto |
| R2 | Caída de proveedor IA en clase en vivo | Media | Medio |
| R3 | Cola de jobs detenida (sin worker) | Media | Alto |
| R4 | Crecimiento de `audit_logs` sin retención | Media | Medio |
| R5 | Credenciales `.env` mal configuradas en prod | Baja | Crítico |
| R6 | Dependencia de un solo servidor monolito | Media | Alto |
| R7 | Tests E2E flaky en CI | Baja | Bajo |
| R8 | SQLite en tests vs MySQL en prod (diferencias SQL) | Baja | Medio |

---

## 3. Dependencias externas

| Dependencia | Uso | Si falla |
|-------------|-----|----------|
| OpenAI / Ollama / Gemini | Tutor, copiloto | Modo degradado |
| SMTP institucional | Correos | Solo notificaciones in-app |
| MySQL | Datos | Sistema no disponible |
| Servicios Meet/Zoom | Videoclase | Usuario usa enlace alternativo |
| Mercado Pago / Culqi (futuro) | Pagos | Registro manual |

---

## 4. Costos de IA

- Facturación por tokens en OpenAI; sin límite interno estricto más allá de `throttle:ai`.
- **Mitigación:** cache de respuestas frecuentes (futuro), Ollama en LAN, cuotas por rol docente.
- Monitoreo vía `/intranet/ai-analytics` (uso agregado en auditoría).

---

## 5. Riesgos de producción

| Escenario | Consecuencia | Mitigación documentada |
|-----------|--------------|------------------------|
| Sin backups | Pérdida de datos | `InstitutionBackupService`, guía backup |
| Sin HTTPS | Sesión comprometida | Checklist seguridad |
| `APP_DEBUG=true` | Fuga de información | Checklist producción |
| Permisos excesivos | Escalada interna | Matriz roles, Spatie |

---

## 6. Mitigaciones implementadas o documentadas

| Riesgo | Mitigación |
|--------|------------|
| R1, R2 | `NullAIProvider`, múltiples proveedores, throttle, guía demo fallback |
| R3 | Documentación queue/scheduler, health checks |
| R4 | Política de purga/archivo (operativa, configurar en prod) |
| R5 | `.env.example`, checklist, sin secretos en repo |
| R6 | Docker, backups, escalado vertical inicial |
| R7 | Cypress en pipeline opcional; Feature tests como base |
| R8 | Migraciones probadas en MySQL en staging |

---

## 7. Escalabilidad futura

- **Vertical:** más CPU/RAM, Redis para cache/colas.
- **Horizontal:** requiere sesiones en Redis, storage compartido, load balancer.
- **Limitación actual:** sesiones file/database en un nodo.

---

## 8. Declaración para jurado

El sistema es **funcional y demostrable** en entorno local/staging con datos semilla. Las capacidades marcadas como *preparado* tienen código y tests de contrato, pero **no deben presentarse como producción bancaria o mensajería masiva** sin configuración adicional y cumplimiento legal (LOPD, consentimiento apoderados, PCI).
