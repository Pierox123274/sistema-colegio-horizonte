# language: es
Característica: Estructura académica institucional
  Como personal autorizado de la intranet
  Quiero gestionar niveles, grados, secciones y aulas
  Para definir la organización curricular del colegio

  Escenario: Invitado no accede al listado de niveles
    Cuando visito "/intranet/academic/levels"
    Entonces debo ser redirigido al login
