# language: es
Característica: CMS institucional del sitio público
  Como administrador del colegio
  Quiero gestionar el contenido público desde la intranet
  Para actualizar la web sin modificar código

  Escenario: Administrador accede al panel CMS
    Dado que existe un usuario administrador
    Cuando accede al dashboard CMS
    Entonces ve el módulo de sitio web institucional

  Escenario: Docente no accede al CMS
    Dado que existe un usuario docente
    Cuando intenta acceder al dashboard CMS
    Entonces recibe acceso denegado

  Escenario: Noticia publicada visible en el sitio
    Dado que existe una noticia publicada en el CMS
    Cuando un visitante abre el listado de noticias
    Entonces ve la noticia en la página pública
