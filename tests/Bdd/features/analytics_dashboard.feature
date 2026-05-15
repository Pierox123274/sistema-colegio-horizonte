# language: es
Característica: Dashboard analítico institucional
  Como administrador o docente del sistema
  Quiero consultar indicadores consolidados
  Para tomar decisiones académicas y operativas

  Escenario: Administrador accede al dashboard ejecutivo
    Dado que inicio sesión como administrador
    Cuando visito la ruta de analítica institucional
    Entonces veo indicadores académicos y financieros

  Escenario: Docente accede a analítica de sus secciones
    Dado que inicio sesión como docente con asignaciones
    Cuando visito la analítica del portal docente
    Entonces veo métricas de mis secciones

  Escenario: Estudiante no accede a analítica
    Dado que inicio sesión como estudiante
    Cuando intento visitar la analítica institucional
    Entonces recibo acceso denegado
