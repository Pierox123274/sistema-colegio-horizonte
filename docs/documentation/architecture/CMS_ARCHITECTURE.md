# Arquitectura CMS — Sitio web institucional

## Propósito

Administrar el **sitio público** del colegio: páginas, noticias, galerías, testimonios, hero, menús y configuración visual.

## Componentes

- Modelos bajo `app/Models/Cms/` — páginas, noticias, categorías, galerías, medios, menús, ajustes.  
- Controladores `Intranet/Cms/*` — CRUD con políticas dedicadas.  
- Frontend público: `PublicLayout`, secciones premium, `PublicThemeProvider`.  
- Componentes: `CmsRichTextEditor`, `CmsMediaLibrary`, `CmsImagePicker`.

## Flujo

Administrador edita contenido en intranet → publicación en tablas CMS → rutas públicas (`PublicSiteController`) renderizan Inertia/React.

## Responsabilidades

- Solo roles autorizados (Administrador / permisos CMS) modifican contenido.  
- Medios en biblioteca reutilizable para formularios y páginas.

## Tecnologías

Inertia, almacenamiento de imágenes, editor enriquecido.

## Decisiones técnicas

- **Separación estricta** portal público vs intranet (rutas y layouts distintos).  
- Tema institucional (colores navy / amarillo marca) centralizado.

## Relación con otros módulos

- Independiente del ERP; enlaces de marketing hacia admisión y contacto.  
- No expone datos académicos sensibles.
