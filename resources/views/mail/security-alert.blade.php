<x-mail::message>
# Alerta de seguridad institucional

{{ $message }}

@if(!empty($actionUrl))
<x-mail::button :url="$actionUrl">
Revisar seguridad
</x-mail::button>
@endif

Si no reconoces esta actividad, cambia tu contraseña inmediatamente.
</x-mail::message>
