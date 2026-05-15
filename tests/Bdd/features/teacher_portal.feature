Feature: Portal docente
  Como docente o administrador
  Quiero un espacio de trabajo académico simplificado
  Para concentrarme en asistencia, notas y estudiantes sin módulos sensibles del ERP.

  Scenario: Docente accede a su dashboard
    Given un usuario con rol Docente autenticado
    When visita el portal docente
    Then visualiza el dashboard docente

  Scenario: Administrador puede supervisar el portal docente
    Given un usuario con rol Administrador autenticado
    When visita el portal docente
    Then visualiza el dashboard docente

  Scenario: Secretaria no usa el portal docente
    Given un usuario con rol Secretaria autenticado
    When intenta visitar el portal docente
    Then el sistema deniega el acceso

  Scenario: Estudiante no accede al portal docente
    Given un usuario con rol Estudiante autenticado
    When intenta visitar el portal docente
    Then el sistema deniega el acceso

  Scenario: Apoderado no accede al portal docente
    Given un usuario con rol Apoderado autenticado
    When intenta visitar el portal docente
    Then el sistema deniega el acceso

  Scenario: Docente consulta asistencia, notas y estudiantes
    Given un usuario con rol Docente autenticado
    When abre asistencia, notas y estudiantes en el portal
    Then obtiene respuestas correctas del portal docente
