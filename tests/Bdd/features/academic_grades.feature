Feature: Gestión académica de calificaciones
  Como usuario de intranet
  Quiero gestionar cursos, evaluaciones y notas
  Para consultar historial y reportes académicos

  Scenario: Administrador crea curso y evaluación
    Given un administrador autenticado
    When registra un curso académico
    And crea una evaluación para una sección
    Then la evaluación queda disponible para registro de notas

  Scenario: Docente registra notas
    Given un docente autenticado
    And existe una evaluación activa con estudiantes matriculados
    When registra notas por estudiante
    Then el sistema guarda notas sin duplicar por evaluación y estudiante

  Scenario: Secretaria consulta y exporta reportes
    Given una secretaria autenticada
    When accede al reporte académico
    Then puede descargar PDF y CSV

  Scenario: Roles sin acceso administrativo
    Given un estudiante autenticado
    Then no puede ingresar al módulo académico de notas
    Given un apoderado autenticado
    Then no puede ingresar al módulo académico de notas

