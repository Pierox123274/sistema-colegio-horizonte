<x-mail::message>
# Alerta financiera

{{ $payload['message'] ?? '' }}

**Pensiones vencidas:** {{ $payload['overdue_pensions'] ?? 0 }}

<x-mail::button :url="url('/intranet/pensions')">
Ver pensiones
</x-mail::button>

</x-mail::message>
