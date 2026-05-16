<x-mail::message>
# Verificación de seguridad

{{ $payload['message'] ?? '' }}

**Intentos fallidos (24h):** {{ $payload['failed_logins_24h'] ?? 0 }}

<x-mail::button :url="url('/intranet/security/access-monitor')">
Monitoreo de accesos
</x-mail::button>

</x-mail::message>
