# Roadmap visual — Evolución futura

Plan **prospectivo** del Sistema Colegio Horizonte. No describe funcionalidad ya desplegada; complementa `ROADMAP.md` (raíz) sin modificarlo.

---

## Horizonte temporal

```mermaid
timeline
    title Línea de evolución propuesta
    2026 H2 : PWA y push notifications
           : OAuth Google Calendar completo
    2027 H1 : Pasarelas de pago en producción
           : WhatsApp institucional
    2027 H2 : App móvil (API o Inertia native shell)
           : SaaS multicolegio (tenant)
    2028+    : BI predictivo y data warehouse
           : Videoconferencia API nativa
```

---

## Iniciativas futuras

| Iniciativa | Descripción | Dependencias | Prioridad sugerida |
|------------|-------------|--------------|-------------------|
| **PWA** | Instalable, offline básico para consultas | Service worker, manifest | Media |
| **SaaS multicolegio** | `tenant_id`, aislamiento BD o schema | Refactor auth y facturación | Alta (comercial) |
| **BI predictivo** | Modelos sobre histórico notas/asistencia | Data limpia, ETL | Media |
| **App móvil** | React Native o API REST dedicada | Capa API o Inertia mobile | Media |
| **Integraciones reales** | MP, Culqi, WhatsApp en prod | Credenciales, PCI | Alta |
| **Push notifications** | Firebase FCM | `FirebasePushProvider` activo | Media |
| **Videoconferencia API** | Crear sala Zoom/Meet vía API | Cuentas institucionales | Media |
| **BI avanzado** | Dashboards ejecutivos cross-módulo | Warehouse | Baja–Media |
| **Data warehouse** | Réplica analítica (BigQuery, PG, etc.) | Pipeline nocturno | Baja |

---

## Matriz esfuerzo vs impacto

|  | Bajo esfuerzo | Alto esfuerzo |
|--|---------------|---------------|
| **Alto impacto** | Push + SMTP prod | SaaS multicolegio |
| **Medio impacto** | OAuth Calendar | App móvil |
| **Bajo impacto** | PWA lectura | Data warehouse |

---

## PWA (detalle)

- Cache de assets estáticos y páginas públicas CMS.
- Notificaciones push cuando FCM esté activo.
- **Estado actual:** sitio responsive; PWA no implementada.

---

## SaaS multicolegio

```mermaid
flowchart LR
    subgraph hoy [Monolito actual]
        A[Una institución]
        DB[(MySQL)]
    end
    subgraph futuro [SaaS]
        T1[Tenant A]
        T2[Tenant B]
        DB2[(DB compartida con tenant_id)]
    end
    A --> DB
    T1 --> DB2
    T2 --> DB2
```

---

## Integraciones reales (pagos y mensajería)

| Canal | Estado actual | Siguiente paso |
|-------|---------------|----------------|
| Mercado Pago | Provider + Null fallback | Webhooks + checkout UI |
| Culqi | Provider stub | Certificación Perú |
| WhatsApp | `WhatsAppProvider` preparado | Meta Business API + plantillas |
| Google Calendar | Export parcial | OAuth2 refresh tokens |

---

## BI y analítica avanzada

1. **Fase intermedia:** export CSV/PDF desde dashboards actuales.
2. **Fase avanzada:** warehouse + vistas materializadas + predicción deserción.
3. **Métricas IA:** costo por token, uso por docente (ya base en `ai-analytics`).

---

## Criterios de entrada a producción por iniciativa

| Iniciativa | Criterio mínimo |
|------------|-----------------|
| Pagos en línea | PCI scope, pruebas sandbox, rollback |
| WhatsApp | Opt-in apoderados, plantillas aprobadas |
| SaaS | Tests de aislamiento tenant, backup por cliente |
| PWA | Lighthouse PWA ≥ 80 |

---

## Relación con documentación existente

- Implementado hoy → `REQUIREMENTS_TRACEABILITY_MATRIX.md`, fases en `phases/`.
- Operación → `deployment/`, `security/`.
- Este archivo → visión **estratégica** para jurado e inversión institucional.
