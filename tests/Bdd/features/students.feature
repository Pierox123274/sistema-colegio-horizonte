# language: es
# Escenarios BDD (ISO/IEC 29119) — módulo de estudiantes
# Cobertura automatizada: ver tests/Feature/Intranet/StudentManagementTest.php

Característica: Gestión de estudiantes en la intranet
  Como personal autorizado
  Quiero registrar y consultar fichas de estudiantes
  Para mantener el padrón institucional con control de acceso

  Escenario: Administrador o secretaría listan estudiantes
    Dado que inicio sesión con un rol que puede gestionar estudiantes
    Cuando abro el listado de estudiantes
    Entonces veo la tabla de estudiantes

  Escenario: Secretaría registra un nuevo estudiante
    Dado que inicio sesión como Secretaría
    Cuando envío el formulario de alta con datos válidos y únicos
    Entonces el estudiante queda guardado y puedo ver su ficha

  Escenario: Docente solo consulta
    Dado que inicio sesión como Docente
    Cuando intento abrir el formulario de nuevo estudiante
    Entonces el acceso es denegado

  Escenario: Estudiante o apoderado no acceden al módulo
    Dado que inicio sesión como Estudiante o Apoderado
    Cuando solicito el listado de estudiantes
    Entonces recibo una respuesta prohibida

  Escenario: Validación de unicidad
    Dado que existe un estudiante con un código o documento
    Cuando intento registrar otro con el mismo código o documento
    Entonces la validación falla y no se duplica el registro
