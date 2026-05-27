<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject ?? config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Segoe UI,Roboto,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f4f6f8;padding:24px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(15,23,42,.08);">
                <tr>
                    <td style="background:#0f2744;padding:20px 28px;">
                        <p style="margin:0;color:#f5c518;font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;">I.E.P. Horizonte</p>
                        <h1 style="margin:6px 0 0;color:#ffffff;font-size:20px;font-weight:600;">{{ $heading ?? config('app.name') }}</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px;color:#334155;font-size:15px;line-height:1.6;">
                        {{ $slot }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 28px;background:#f8fafc;border-top:1px solid #e2e8f0;color:#64748b;font-size:12px;">
                        Mensaje institucional automático. No responda a este correo.
                        @if(config('integrations.mail.preview_enabled') && app()->environment('local'))
                            <br><em>Vista previa habilitada en entorno local.</em>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
