Feature: QA integral de plataforma (Fase 29)
  Como equipo institucional
  Quiero que rutas, permisos y portales estén coherentes
  Para operar y demostrar el sistema con confianza

  Scenario: Invitado no accede al panel intranet
    When un invitado visita "/intranet/dashboard"
    Then es redirigido al login

  Scenario: Administrador accede a módulos críticos
    Given existe un usuario administrador autenticado
    When visita "/intranet/dashboard"
    Then visualiza el panel principal
    And puede acceder a "/notifications"
    And puede acceder a "/intranet/system/health"

  Scenario: Secretaría tiene acceso operativo sin módulos solo admin
    Given existe un usuario secretaría autenticado
    When visita "/intranet/students"
    Then la respuesta es exitosa
    When visita "/intranet/system/health"
    Then la respuesta es prohibida

  Scenario: Docente es redirigido al portal docente
    Given existe un usuario docente autenticado
    When visita "/intranet/dashboard"
    Then es redirigido al portal docente

  Scenario: Estudiante accede a LMS y gamificación en su portal
    Given existe un estudiante autenticado con perfil académico
    When visita "/student/dashboard"
    Then la respuesta es exitosa
    When visita "/student/gamification"
    Then la respuesta es exitosa
