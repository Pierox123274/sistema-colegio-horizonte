# Caso de uso — Administrador gestiona el sitio web (CMS)

## Identificación

| Campo | Valor |
|-------|--------|
| **ID** | UC-CMS-003 |
| **Nombre** | Publicar y actualizar contenido institucional |
| **Módulo** | CMS |

## Actores

| Tipo | Actor |
|------|--------|
| **Principal** | Administrador |
| **Secundarios** | Visitante público (consumidor), Sistema de caché/archivos |

## Objetivo

Permitir la administración del sitio público (páginas, noticias, galerías, hero, menús, ajustes) sin despliegue de código.

## Precondiciones

1. Rol Administrador activo.
2. Tablas CMS migradas (`cms_*`).
3. Acceso a `/intranet/cms/*`.

## Flujo principal

1. El administrador ingresa a **Sitio web** en la intranet.
2. Edita contenido (p. ej. noticia: crear en `/intranet/cms/news/create`).
3. Opcional: selecciona imagen desde **Biblioteca de medios** (`CmsMediaLibrary`).
4. Guarda; `CmsContentService` persiste en base de datos.
5. El visitante ve el cambio en rutas públicas (`/`, `/noticias`, etc.) vía `CmsPublicService`.

## Flujos alternativos

| ID | Condición | Comportamiento |
|----|-----------|----------------|
| FA-1 | Página en borrador | No visible en menú público hasta publicación. |
| FA-2 | Reemplazo de imagen hero | Actualización en `cms_hero_slides` y refresco visual en home. |

## Excepciones

| ID | Excepción | Respuesta |
|----|-----------|-----------|
| EX-1 | Archivo no permitido | Validación de tipo/tamaño en subida de medios. |
| EX-2 | Secretaría intenta CMS | 403 — CMS restringido a Administrador. |

## Resultado esperado

Contenido institucional coherente y actualizado en el sitio público, alineado con la identidad visual del colegio.

## Evidencia

- `tests/Feature/CMS/CmsManagementTest.php`, Cypress `cms-management.cy.ts`.
