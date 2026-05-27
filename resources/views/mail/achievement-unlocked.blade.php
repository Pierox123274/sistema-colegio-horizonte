<x-mail::message>
# Logro desbloqueado

{{ $message }}

@if(!empty($actionUrl))
<x-mail::button :url="$actionUrl">
Ver mi progreso
</x-mail::button>
@endif

Sigue construyendo una trayectoria académica destacada.
</x-mail::message>
