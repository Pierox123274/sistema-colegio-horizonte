# Guía profesional de demostración

Manual para presentar el **Sistema Colegio Horizonte** ante jurado, directivos o docentes. Incluye orden sugerido, rutas, cuentas de prueba y plan B si falla la IA.

> **Seguridad:** no exponer `.env` ni API keys en pantalla compartida.

---

## Preparación previa (15 minutos)

```bash
php artisan migrate --seed
php artisan db:seed --class=TeacherDemoUserSeeder
php artisan db:seed --class=StudentPortalDemoSeeder
npm run build
php artisan serve
# Terminal 2: npm run dev  (o usar build ya generado)
```

Verificar: `php artisan test` (opcional, confianza pre-demo).

---

## Cuentas de demostración

| Rol | Email | Contraseña | Origen |
|-----|-------|------------|--------|
| Administrador | `test@example.com` | (factory / ver seeder) | `DatabaseSeeder` |
| Docente | `docente@demo.com` | `password` | `TeacherDemoUserSeeder` |
| Estudiante | `estudiante@demo.com` | `password` | `StudentPortalDemoSeeder` |

*Ejecutar seeders explícitos si el admin factory no tiene contraseña conocida; en local puede restablecerse con `php artisan tinker`.*

---

## Orden de presentación recomendado (45–60 min)

| # | Bloque | Duración | Impacto |
|---|--------|----------|---------|
| 1 | Sitio público + CMS | 8 min | Alto — identidad institucional |
| 2 | Login y dashboard admin | 7 min | Alto — ERP unificado |
| 3 | Académico + analítica | 8 min | Medio-alto |
| 4 | LMS + estudiante | 10 min | Muy alto |
| 5 | Adaptive + tutor IA | 10 min | Muy alto — innovación |
| 6 | Copiloto docente | 7 min | Alto |
| 7 | Gamificación + notificaciones | 5 min | Medio |
| 8 | Integraciones + seguridad | 5 min | Medio — madurez |

---

## Discurso sugerido (elevator pitch)

> «Horizonte integra en una sola plataforma la gestión escolar, el aula virtual, el sitio web institucional y asistentes de IA pedagógica, con auditoría y pruebas automatizadas. No es un LMS aislado: es el sistema nervioso digital del colegio.»

---

## Demo pública (sin login)

| Paso | Ruta | Qué mostrar |
|------|------|-------------|
| 1 | `/` | Home, hero, noticias |
| 2 | `/nosotros` | Misión, historia |
| 3 | `/niveles` | Oferta educativa |
| 4 | `/noticias` | CMS en acción (contenido dinámico) |
| 5 | Toggle tema | Claro/oscuro (`PublicThemeProvider`) |

**Mensaje clave:** el colegio controla su imagen sin programadores.

---

## Demo administrador

| Paso | Ruta | Qué mostrar |
|------|------|-------------|
| 1 | `/login` | Acceso seguro |
| 2 | `/intranet/dashboard` | KPIs resumen |
| 3 | `/intranet/students` | Gestión estudiantes |
| 4 | `/intranet/analytics` | Gráficos Recharts |
| 5 | `/intranet/cms/news` | CMS |
| 6 | `/intranet/security/audit-logs` | Trazabilidad |
| 7 | `/intranet/integrations` | Estado integraciones |
| 8 | `/intranet/ai-analytics` | Uso IA agregado |

**Mensaje clave:** gobierno, datos y cumplimiento.

---

## Demo docente

| Paso | Ruta | Qué mostrar |
|------|------|-------------|
| 1 | Login `docente@demo.com` | Portal dedicado |
| 2 | `/teacher/dashboard` | Resumen clases |
| 3 | `/teacher/classrooms` | Aulas virtuales |
| 4 | `/teacher/ai-copilot` | Generación rúbricas/actividades |
| 5 | `/teacher/students-risk` | Riesgo académico |
| 6 | Reuniones docente | Videoclase con enlace |

**Mensaje clave:** menos tiempo administrativo, más foco pedagógico.

---

## Demo estudiante

| Paso | Ruta | Qué mostrar |
|------|------|-------------|
| 1 | Login `estudiante@demo.com` | UX estudiante |
| 2 | `/student/dashboard` | Resumen |
| 3 | `/student/classrooms` | LMS |
| 4 | `/student/diagnostic` | Adaptativo |
| 5 | `/student/ai-tutor` | Tutor IA |
| 6 | `/student/learning-path` | Recomendaciones |
| 7 | `/notifications` | Centro notificaciones |

**Mensaje clave:** aprendizaje personalizado y acompañamiento 24/7 (con supervisión docente).

---

## Funcionalidades de mayor impacto visual

1. Sitio público premium + dark mode  
2. Dashboard analítica con gráficos  
3. Aula virtual con examen/tarea  
4. Tutor IA en conversación  
5. Copiloto docente generando rúbrica  
6. Panel auditoría en tiempo real  
7. Gamificación (barra XP / logros)

---

## Fallback si falla la IA

| Síntoma | Acción inmediata |
|---------|------------------|
| Error API / timeout | Explicar `NullAIProvider` y arquitectura desacoplada; mostrar **analítica IA** histórica |
| Sin `OPENAI_API_KEY` | Cambiar a `AI_PROVIDER=ollama` si hay Ollama local, o mostrar capturas en `screenshots/images/` |
| Throttle 429 | Esperar 1 min o usar pantalla copiloto ya generada en sesión anterior |

**Narrativa de respaldo:** «El sistema sigue operativo sin IA; la IA es una capa opcional con fallback documentado.»

---

## Qué evitar en vivo

- Editar `.env` en projector  
- Borrar datos sin backup  
- Prometer cobro WhatsApp en producción (está preparado, no activo por defecto)  
- Crear usuario producción real sin consentimiento  

---

## Cierre sugerido

1. Mostrar `php artisan test` (resultado verde) — 30 s.  
2. Mencionar 336 tests y documentación en `docs/documentation/`.  
3. Abrir `REQUIREMENTS_TRACEABILITY_MATRIX.md` si preguntan alcance.  

---

## Referencias

- Mockups: `mockups/`  
- Capturas: `screenshots/SCREENSHOT_CAPTURE_CHECKLIST.md`  
- Riesgos IA: `RISKS_AND_LIMITATIONS.md`
