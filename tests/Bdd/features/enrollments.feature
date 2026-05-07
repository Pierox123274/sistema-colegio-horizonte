# language: es
# Escenarios BDD (ISO/IEC 29119) — módulo de matrículas
# Cobertura automatizada: ver tests/Feature/Intranet/EnrollmentManagementTest.php

Característica: Matrículas institucionales
  Como administración o secretaría
  Quiero registrar matrículas por año académico y ubicación curricular
  Para mantener la matrícula oficial sin duplicados activos

  Escenario: Administrador o secretaría crean una matrícula
    Dado que existen año académico, estructura curricular y estudiante
    Cuando registran una matrícula con datos coherentes
    Entonces el registro queda guardado con código y estado definidos

  Escenario: Docente solo consulta
    Dado que existe una matrícula en el sistema
    Cuando un docente abre el listado o el detalle
    Entonces puede ver la información
    Pero no puede abrir el formulario de alta ni guardar cambios

  Escenario: Estudiante o apoderado no administran matrículas
    Dado que el usuario tiene rol estudiante o apoderado
    Cuando solicita el módulo de matrículas
    Entonces el acceso es denegado

  Escenario: No hay doble matrícula activa en el mismo año
    Dado que el estudiante ya tiene una matrícula pendiente o matriculada en ese año
    Cuando se intenta registrar otra para el mismo año en estado activo
    Entonces la operación se rechaza con mensaje de validación
