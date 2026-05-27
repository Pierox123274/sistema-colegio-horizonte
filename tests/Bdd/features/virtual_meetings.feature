Feature: Videoclases y reuniones institucionales
  Como docente o estudiante
  Quiero programar y unirme a videoclases
  Para continuar la enseñanza en modalidad virtual

  Scenario: Docente programa una videoclase vinculada al aula virtual
    Given existe un docente con aula virtual y estudiantes matriculados
    When el docente crea una videoclase con proveedor Google Meet
    Then la reunión queda programada con enlace de acceso

  Scenario: Estudiante participante accede al listado de videoclases
    Given existe una videoclase con el estudiante como participante
    When el estudiante visita su portal de videoclases
    Then visualiza la sesión próxima

  Scenario: Administrador consulta métricas institucionales
    Given existe un administrador autenticado
    When visita el panel de videoclases en intranet
    Then visualiza métricas de uso y reuniones
