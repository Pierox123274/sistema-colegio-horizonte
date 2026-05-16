<x-mail::message>
# Resumen diario institucional

**Estudiantes:** {{ $metrics['students_total'] ?? 0 }}  
**Usuarios:** {{ $metrics['users_total'] ?? 0 }}  
**Matrículas pendientes:** {{ $metrics['enrollments_pending'] ?? 0 }}  
**Pagos registrados hoy:** {{ $metrics['payments_today'] ?? 0 }}

_Generado: {{ $metrics['generated_at'] ?? '' }}_

</x-mail::message>
