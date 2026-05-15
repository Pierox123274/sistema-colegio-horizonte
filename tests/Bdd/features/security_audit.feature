# language: es
Característica: Seguridad y auditoría institucional
  Como administrador del sistema
  Quiero auditar accesos y actividad
  Para cumplir trazabilidad ISO 27001

  Escenario: Administrador consulta auditoría
    Dado que inicio sesión como administrador
    Cuando visito el registro de auditoría institucional
    Entonces veo el timeline y la tabla de logs

  Escenario: Docente solo ve su historial
    Dado que inicio sesión como docente
    Cuando visito el registro de auditoría institucional
    Entonces solo veo eventos propios

  Escenario: Estudiante no accede a seguridad
    Dado que inicio sesión como estudiante
    Cuando intento visitar el registro de auditoría institucional
    Entonces recibo acceso denegado
