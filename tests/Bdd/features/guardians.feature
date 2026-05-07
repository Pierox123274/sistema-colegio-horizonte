# language: es
# BDD (ISO/IEC 29119) — apoderados y vínculos con estudiantes
# Automatización: tests/Feature/Intranet/GuardianManagementTest.php

Característica: Gestión de apoderados
  Como personal de secretaría o administración
  Quiero registrar apoderados y asociarlos a estudiantes
  Para mantener datos familiares y de contacto confiables

  Escenario: Administrador o secretaría crean un apoderado
    Dado que inicio sesión con rol administrativo adecuado
    Cuando envío el formulario de apoderado con datos válidos
    Entonces el apoderado queda registrado

  Escenario: Docente solo consulta
    Dado que inicio sesión como Docente
    Cuando intento crear un apoderado
    Entonces el acceso es denegado

  Escenario: Estudiante y apoderado no acceden al módulo administrativo
    Dado que inicio sesión como Estudiante o Apoderado
    Cuando solicito el listado de apoderados
    Entonces recibo una respuesta prohibida

  Escenario: Vínculo con estudiante y responsable económico
    Dado que existe un estudiante en el sistema
    Cuando registro un apoderado vinculado y marco responsable económico
    Entonces la relación en base de datos refleja el vínculo y la marca económica

  Escenario: Documento duplicado
    Dado que ya existe un apoderado con un número de documento
    Cuando intento registrar otro con el mismo documento
    Entonces la validación falla
