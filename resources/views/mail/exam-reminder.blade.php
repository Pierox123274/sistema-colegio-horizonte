<x-mail::message>
# Recordatorio de evaluación

{{ $message }}

@if(!empty($actionUrl))
<x-mail::button :url="$actionUrl">
Revisar evaluación
</x-mail::button>
@endif

Revisa fecha y hora para organizar tu estudio.
</x-mail::message>
