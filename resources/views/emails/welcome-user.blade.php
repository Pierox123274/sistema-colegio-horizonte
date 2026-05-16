<x-mail::message>
# Bienvenido, {{ $user->name }}

Se ha creado su cuenta en **{{ config('app.name') }}**.

Puede iniciar sesión con el correo **{{ $user->email }}**.

<x-mail::button :url="url('/login')">
Iniciar sesión
</x-mail::button>

Si no esperaba este mensaje, ignore este correo.

</x-mail::message>
