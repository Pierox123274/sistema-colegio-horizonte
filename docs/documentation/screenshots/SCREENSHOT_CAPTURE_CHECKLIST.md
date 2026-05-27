# Checklist avanzado de capturas de pantalla

Complementa [SCREENSHOTS_GUIDE.md](./SCREENSHOTS_GUIDE.md) **sin modificarlo**. Uso: material de tesis, portafolio y presentación con evidencia visual.

---

## Convención de nombres

```
{contexto}-{pantalla}-{variante}.png
```

| Segmento | Valores |
|----------|---------|
| contexto | `public`, `auth`, `admin`, `teacher`, `student`, `lms`, `cms`, `ai`, `analytics`, `gamification`, `meetings`, `notifications`, `integrations`, `security` |
| pantalla | kebab-case descriptivo | 
| variante | `light`, `dark`, `mobile` (opcional) |

**Ejemplos:** `public-home-light.png`, `student-ai-tutor-dark.png`, `admin-dashboard-light.png`

---

## Resolución y formato

| Uso | Resolución | Formato |
|-----|------------|---------|
| Documento / PDF | 1920×1080 | PNG |
| Portafolio web | 1440×900 | PNG o WebP |
| Móvil responsive | 390×844 | PNG |
| Impresión | 2560×1440 | PNG |

- Escala del navegador: **100%** (no zoom).  
- Fuente del SO: 100%; evitar escalado fraccional en Windows.  
- Recortar barras del SO salvo que se quiera mostrar ventana completa.

---

## Modo claro / oscuro

| Ámbito | Cómo activar | Capturas mínimas |
|--------|--------------|------------------|
| Sitio público | Toggle en header (`PublicThemeProvider`) | Home light + dark |
| Intranet | Si aplica tema oscuro en layout | Dashboard admin light |
| IA / LMS | Preferir light para impresión; dark para slide “premium” | 1 par en tutor IA |

**Regla:** mismo contenido y misma resolución al comparar light/dark.

---

## Orden sugerido de captura (sesión única ~90 min)

### Bloque A — Público (15 min)

- [ ] `public-home-light.png` — `/`
- [ ] `public-home-dark.png` — `/` (dark)
- [ ] `public-noticias-light.png` — `/noticias`
- [ ] `public-niveles-light.png` — `/niveles`
- [ ] `public-admision-light.png` — `/admision`

### Bloque B — Autenticación (5 min)

- [ ] `auth-login-light.png` — `/login`
- [ ] `auth-login-dark.png` — si aplica

### Bloque C — Admin (20 min)

- [ ] `admin-dashboard-light.png` — `/intranet/dashboard`
- [ ] `admin-students-light.png` — `/intranet/students`
- [ ] `admin-analytics-light.png` — `/intranet/analytics`
- [ ] `admin-cms-news-light.png` — `/intranet/cms/news`
- [ ] `admin-ai-analytics-light.png` — `/intranet/ai-analytics`
- [ ] `admin-integrations-light.png` — `/intranet/integrations`
- [ ] `security-audit-logs-light.png` — `/intranet/security/audit-logs`

### Bloque D — Docente (15 min)

- [ ] `teacher-dashboard-light.png`
- [ ] `teacher-classrooms-light.png`
- [ ] `teacher-ai-copilot-light.png`
- [ ] `teacher-students-risk-light.png`

### Bloque E — Estudiante (20 min)

- [ ] `student-dashboard-light.png`
- [ ] `lms-classroom-light.png` — detalle aula
- [ ] `lms-exam-attempt-light.png` — intento examen (sin datos sensibles reales)
- [ ] `student-diagnostic-light.png`
- [ ] `student-ai-tutor-light.png`
- [ ] `student-learning-path-light.png`
- [ ] `gamification-profile-light.png`

### Bloque F — Transversal (10 min)

- [ ] `notifications-center-light.png`
- [ ] `meetings-student-light.png`
- [ ] `admin-gamification-light.png`

### Bloque G — Móvil (15 min)

- [ ] `public-home-mobile.png` — DevTools iPhone 14
- [ ] `student-dashboard-mobile.png`

---

## Qué evitar

| Evitar | Motivo |
|--------|--------|
| Datos personales reales (DNI, teléfonos) | Privacidad / LOPD |
| Emails de producción | Seguridad |
| API keys en pantalla | Filtración |
| Scroll cortado a mitad de tabla | Presentación poco profesional |
| Estados de error 500 | Unless documentando troubleshooting |
| Ventanas con 20 pestañas | Distracción |

---

## Ruta de almacenamiento

```
docs/documentation/screenshots/images/
```

Referenciar en Markdown:

```markdown
![Dashboard administrativo](../screenshots/images/admin-dashboard-light.png)
```

---

## Checklist de calidad antes de entregar

- [ ] Nombre según convención  
- [ ] Sin barra de contraseña visible  
- [ ] Texto legible (contraste WCAG básico)  
- [ ] Misma ventana de navegador (Chrome/Edge recomendado)  
- [ ] Fecha de captura anotada en README de entrega (opcional)  

---

## Herramientas recomendadas

- **Windows:** Win+Shift+S, Snipping Tool, ShareX  
- **Recorte full page:** extensión “GoFullPage” solo si no distorsiona layout Inertia  
- **Anotaciones:** opcional para tesis; mantener imagen original sin marcas en repo
