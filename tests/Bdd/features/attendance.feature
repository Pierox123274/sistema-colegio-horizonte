Feature: Gestión de asistencia académica
  Como usuario de intranet
  Quiero registrar y consultar asistencia por sección y fecha
  Para mantener trazabilidad académica diaria.

  Scenario: Administrador registra asistencia masiva
    Given existe un administrador autenticado
    And existe una sección con estudiantes matriculados
    When registra asistencia para la fecha actual
    Then el sistema guarda los estados por estudiante

  Scenario: Docente registra asistencia masiva
    Given existe un docente autenticado
    And existe una sección con estudiantes matriculados
    When registra asistencia para la fecha actual
    Then el sistema guarda los estados por estudiante

  Scenario: Secretaria solo consulta
    Given existe una secretaria autenticada
    When intenta registrar asistencia
    Then el sistema rechaza la operación
    And puede consultar historial

  Scenario: No se duplica asistencia por estudiante-fecha-sección
    Given existe un registro de asistencia previo para un estudiante
    When se vuelve a guardar la asistencia del mismo estudiante, fecha y sección
    Then el sistema actualiza el registro existente sin duplicar

