# language: es
# Escenarios BDD (ISO/IEC 29119) — autenticación e intranet
# Ejecución automatizada: ver tests PHPUnit en tests/Feature/Auth y tests/Feature/Intranet.

Característica: Autenticación y acceso a la intranet
  Como usuario del sistema
  Quiero iniciar sesión de forma segura
  Para acceder solo a las funciones autorizadas según mi rol

  Escenario: Inicio de sesión exitoso
    Dado que existe un usuario con correo "user@example.com" y contraseña válida y rol de intranet
    Cuando envío credenciales correctas al endpoint de login
    Entonces quedo autenticado
    Y soy redirigido al panel de la intranet

  Escenario: Inicio de sesión inválido
    Dado que existe un usuario con correo "user@example.com"
    Cuando envío una contraseña incorrecta al endpoint de login
    Entonces no quedo autenticado

  Escenario: Acceso a ruta protegida sin sesión
    Dado que no estoy autenticado
    Cuando solicito el panel de la intranet
    Entonces soy redirigido a la pantalla de login

  Escenario: Acceso a intranet sin rol asignado
    Dado que estoy autenticado sin ningún rol de intranet
    Cuando solicito el panel de la intranet
    Entonces recibo una respuesta prohibida

  Escenario: Acceso por rol
    Dado que estoy autenticado con el rol "Docente"
    Cuando solicito el panel de la intranet
    Entonces la respuesta es correcta
