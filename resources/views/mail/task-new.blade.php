<x-mail::message>
# Nueva tarea asignada

{{ $message }}

@if(!empty($actionUrl))
<x-mail::button :url="$actionUrl">
Ir a la tarea
</x-mail::button>
@endif

Mantén tu progreso académico al día.
</x-mail::message>
