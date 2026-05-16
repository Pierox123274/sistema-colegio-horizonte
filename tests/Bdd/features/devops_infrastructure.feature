# language: es
Característica: Infraestructura DevOps institucional
  Como administrador del sistema
  Quiero monitorear salud, colas y respaldos
  Para operar el entorno con trazabilidad ISO

  Escenario: Administrador ve el panel de salud
    Dado que inicio sesión como administrador
    Cuando visito el panel de salud del sistema
    Entonces veo el estado de la base de datos y la cola

  Escenario: Secretaría no accede al panel técnico
    Dado que inicio sesión como secretaría
    Cuando intento visitar el panel de salud del sistema
    Entonces recibo acceso denegado
