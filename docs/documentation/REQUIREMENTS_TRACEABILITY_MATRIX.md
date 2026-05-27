# Matriz de trazabilidad de requerimientos

Documento académico que relaciona requerimientos funcionales con módulos, estado de implementación y evidencia verificable (pruebas, rutas, documentación).

**Leyenda de estado:** `Implementado` | `Parcial` | `Preparado` (infraestructura sin conexión productiva)

---

## ERP escolar

| ID | Requerimiento | Módulo | Estado | Evidencia |
|----|---------------|--------|--------|-----------|
| ERP-01 | CRUD estudiantes y ficha académica | ERP | Implementado | `StudentController`, `StudentManagementTest` |
| ERP-02 | Gestión de apoderados y vínculos | ERP | Implementado | `GuardianController`, `guardians.feature` |
| ERP-03 | Matrículas por año académico | ERP | Implementado | `EnrollmentController`, `enrollments.cy.ts` |
| ERP-04 | Estructura niveles/secciones/aulas | Académico | Implementado | `AcademicStructureTest`, `academic-structure.cy.ts` |
| ERP-05 | Registro de notas y evaluaciones | Académico | Implementado | `AcademicGradesManagementTest` |
| ERP-06 | Control de asistencia | Académico | Implementado | `AttendanceManagementTest`, `attendance.cy.ts` |
| ERP-07 | Pensiones y pagos con comprobante | Finanzas | Implementado | `FinanceManagementTest`, `payment-receipts.cy.ts` |
| ERP-08 | Inventario y ventas de útiles | Inventario | Implementado | `InventoryManagementTest`, `inventory.cy.ts` |
| ERP-09 | Comunicados institucionales | Comunicación | Implementado | `AnnouncementManagementTest` |
| ERP-10 | Dashboard administrativo | ERP | Implementado | `IntranetDashboardUiTest` |

---

## LMS

| ID | Requerimiento | Módulo | Estado | Evidencia |
|----|---------------|--------|--------|-----------|
| LMS-01 | Aulas virtuales por sección | LMS | Implementado | `VirtualClassroomTest` |
| LMS-02 | Recursos y anuncios en aula | LMS | Implementado | Modelos `VirtualResource`, `VirtualClassroomAnnouncement` |
| LMS-03 | Tareas y entregas | LMS | Implementado | `AssignmentService`, portal estudiante |
| LMS-04 | Exámenes en línea con intentos | LMS | Implementado | `OnlineExamService`, `ExamAttempt.tsx` |
| LMS-05 | Calendario académico LMS | LMS | Implementado | `LMSCalendarService` |
| LMS-06 | Portal docente LMS | LMS | Implementado | `TeacherPortalTest`, `teacher-portal.cy.ts` |

---

## CMS

| ID | Requerimiento | Módulo | Estado | Evidencia |
|----|---------------|--------|--------|-----------|
| CMS-01 | Páginas y secciones públicas | CMS | Implementado | `CmsManagementTest` |
| CMS-02 | Noticias y categorías | CMS | Implementado | `CmsNews`, rutas públicas `/noticias` |
| CMS-03 | Galerías e imágenes | CMS | Implementado | `CmsMediaService`, biblioteca medios |
| CMS-04 | Hero y menús configurables | CMS | Implementado | `CmsHeroSlide`, `CmsMenu` |
| CMS-05 | Tema claro/oscuro sitio público | CMS/UX | Implementado | `PublicThemeProvider` |

---

## IA

| ID | Requerimiento | Módulo | Estado | Evidencia |
|----|---------------|--------|--------|-----------|
| AI-01 | Tutor IA estudiante | IA | Implementado | `AITutorTest`, `ai-tutor.cy.ts` |
| AI-02 | Proveedores desacoplados | IA | Implementado | `AIProviderInterface`, OpenAI/Ollama/Gemini/Null |
| AI-03 | Copiloto docente | IA | Implementado | `AdvancedAIFeaturesTest`, Fase 31 |
| AI-04 | Analítica IA administrativa | IA | Implementado | `/intranet/ai-analytics`, `AdvancedAIAnalyticsService` |
| AI-05 | Riesgo académico asistido | IA | Implementado | `AcademicRiskAnalysisService`, panel docente |
| AI-06 | Coach de aprendizaje estudiante | IA | Parcial | `StudentLearningCoachService` |

---

## Gamificación

| ID | Requerimiento | Módulo | Estado | Evidencia |
|----|---------------|--------|--------|-----------|
| GAM-01 | Perfil XP y niveles | Gamificación | Implementado | `GamificationTest` |
| GAM-02 | Logros e insignias | Gamificación | Implementado | `Achievement`, `StudentAchievement` |
| GAM-03 | Retos estudiantiles | Gamificación | Implementado | `Challenge`, `StudentChallenge` |
| GAM-04 | Panel admin gamificación | Gamificación | Implementado | `/intranet/gamification` |

---

## Videoclases

| ID | Requerimiento | Módulo | Estado | Evidencia |
|----|---------------|--------|--------|-----------|
| MEET-01 | CRUD reuniones virtuales | Meetings | Implementado | `VirtualMeetingsTest` |
| MEET-02 | Enlace externo Meet/Zoom/Teams | Meetings | Implementado | URL manual en reunión |
| MEET-03 | Asistencia a reunión | Meetings | Parcial | `MeetingAttendance` |
| MEET-04 | API nativa videoconferencia | Meetings | Preparado | Roadmap FUTURE_ROADMAP.md |

---

## Notificaciones

| ID | Requerimiento | Módulo | Estado | Evidencia |
|----|---------------|--------|--------|-----------|
| NOT-01 | Bandeja in-app | Notificaciones | Implementado | `NotificationSystemTest` |
| NOT-02 | Preferencias por categoría | Notificaciones | Implementado | `UserNotificationPreference` |
| NOT-03 | Jobs recordatorios | Notificaciones | Implementado | `Send*RemindersJob` |
| NOT-04 | Push móvil | Notificaciones | Preparado | `NullPushProvider` |

---

## Analytics

| ID | Requerimiento | Módulo | Estado | Evidencia |
|----|---------------|--------|--------|-----------|
| AN-01 | Dashboard analítica intranet | Analytics | Implementado | `AnalyticsDashboardTest` |
| AN-02 | KPIs financieros | Analytics | Implementado | `FinancialAnalyticsService` |
| AN-03 | Inventario analítico | Analytics | Implementado | `InventoryAnalyticsService` |
| AN-04 | BI predictivo institucional | Analytics | Preparado | FUTURE_ROADMAP.md |

---

## Seguridad

| ID | Requerimiento | Módulo | Estado | Evidencia |
|----|---------------|--------|--------|-----------|
| SEC-01 | Roles Spatie (5 roles) | Seguridad | Implementado | `IntranetRole`, `RoleSeeder` |
| SEC-02 | Auditoría centralizada | Seguridad | Implementado | `AuditSecurityTest`, `audit_logs` |
| SEC-03 | Intentos de login | Seguridad | Implementado | `LoginAttempt`, panel seguridad |
| SEC-04 | Sesiones y salud sistema | Seguridad | Implementado | `SystemHealthService`, `ProductionReadinessTest` |
| SEC-05 | OAuth SSO institucional | Seguridad | Preparado | Documentado como futuro |

---

## Integraciones

| ID | Requerimiento | Módulo | Estado | Evidencia |
|----|---------------|--------|--------|-----------|
| INT-01 | SMTP institucional | Integraciones | Parcial | `InstitutionMailService`, .env mail |
| INT-02 | Webhooks entrantes | Integraciones | Implementado | `webhooks.php`, `ExternalIntegrationsTest` |
| INT-03 | Google Calendar export | Integraciones | Parcial | `GoogleCalendarProvider` / Null fallback |
| INT-04 | Pasarela Mercado Pago / Culqi | Integraciones | Preparado | Providers stub + panel |
| INT-05 | WhatsApp Business | Integraciones | Preparado | `WhatsAppProvider` / Null |
| INT-06 | Panel estado integraciones | Integraciones | Implementado | `/intranet/integrations` |

---

## Resumen de cobertura de requerimientos

| Área | Total IDs | Implementado | Parcial | Preparado |
|------|-----------|--------------|---------|-----------|
| ERP + Finanzas | 10 | 10 | 0 | 0 |
| LMS | 6 | 6 | 0 | 0 |
| CMS | 5 | 5 | 0 | 0 |
| IA | 6 | 5 | 1 | 0 |
| Gamificación | 4 | 4 | 0 | 0 |
| Videoclases | 4 | 2 | 1 | 1 |
| Notificaciones | 4 | 3 | 0 | 1 |
| Analytics | 4 | 3 | 0 | 1 |
| Seguridad | 5 | 4 | 0 | 1 |
| Integraciones | 6 | 2 | 2 | 2 |
| **Total** | **54** | **44 (~81%)** | **4 (~7%)** | **6 (~11%)** |

*Porcentajes orientativos para presentación académica; no implican cobertura de código al 81%.*
