<x-mail::message>
# Alerta académica

{{ $payload['message'] ?? '' }}

**Matrículas pendientes:** {{ $payload['pending_enrollments'] ?? 0 }}

<x-mail::button :url="url('/intranet/enrollments')">
Ver matrículas
</x-mail::button>

</x-mail::message>
