<x-mail::message>
# {{ $title }}

{{ $message }}

**Categoría:** {{ strtoupper($category ?? 'system') }}  
**Prioridad:** {{ strtoupper($priority ?? 'medium') }}

@if(!empty($actionUrl))
<x-mail::button :url="$actionUrl">
{{ $actionLabel ?? 'Ver detalle' }}
</x-mail::button>
@endif

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
