<x-mail::message>
# Nuevo comunicado

**{{ $announcement->title }}**

Publicado el {{ $announcement->starts_at?->translatedFormat('d/m/Y H:i') ?? '' }}.

<x-mail::button :url="url('/intranet/announcements/'.$announcement->id)">
Ver comunicado
</x-mail::button>

</x-mail::message>
