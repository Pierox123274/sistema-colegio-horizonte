# Feature: Aula virtual, tareas y evaluaciones online (LMS)
# Fase 23 — criterios BDD (ISO/IEC 29119 documentación)

Feature: Plataforma LMS institucional integrada al ERP académico
  Como docente, estudiante o administrador
  Quiero gestionar aulas virtuales, tareas y evaluaciones online
  Para centralizar el aprendizaje con trazabilidad y seguridad por sección

  Scenario: Docente accede al listado de aulas virtuales
    Given un usuario autenticado con rol "Docente"
    When solicita la ruta de aulas virtuales del portal docente
    Then la respuesta es exitosa y muestra el listado de aulas

  Scenario: Estudiante accede al listado de aulas virtuales
    Given un usuario autenticado con rol "Estudiante" y ficha de estudiante
    When solicita la ruta de aulas virtuales del portal estudiante
    Then la respuesta es exitosa y muestra el listado de aulas

  Scenario: Estudiante accede al calendario académico
    Given un usuario autenticado con rol "Estudiante" y ficha de estudiante
    When solicita la ruta de calendario del portal estudiante
    Then la respuesta es exitosa

  Scenario: Docente accede al calendario académico
    Given un usuario autenticado con rol "Docente"
    When solicita la ruta de calendario del portal docente
    Then la respuesta es exitosa

  Scenario: Administrador accede al resumen institucional LMS
    Given un usuario autenticado con rol "Administrador"
    When solicita la ruta de resumen LMS en intranet
    Then la respuesta es exitosa

  Scenario: Secretaría no accede al resumen institucional LMS
    Given un usuario autenticado con rol "Secretaria"
    When solicita la ruta de resumen LMS en intranet
    Then la respuesta indica prohibición de acceso
