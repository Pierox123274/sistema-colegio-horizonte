<?php

namespace App\Support;

use Illuminate\Support\Facades\Config;

/**
 * Validación básica de variables críticas (producción / despliegue).
 */
final class EnvSecurityValidator
{
    /**
     * @return list<string> Lista de advertencias o errores legibles.
     */
    public function validate(bool $strictProduction = false): array
    {
        $issues = [];

        if (Config::get('app.key') === '' || Config::get('app.key') === 'base64:') {
            $issues[] = 'APP_KEY no está definido correctamente.';
        }

        if ($strictProduction && Config::get('app.env') === 'production') {
            if (Config::get('app.debug')) {
                $issues[] = 'APP_DEBUG debe ser false en producción.';
            }
            if (! filter_var(env('SESSION_SECURE_COOKIE'), FILTER_VALIDATE_BOOL)) {
                $issues[] = 'SESSION_SECURE_COOKIE debería ser true en producción con HTTPS.';
            }
            if (! str_starts_with((string) Config::get('app.url'), 'https://')) {
                $issues[] = 'APP_URL debe usar https:// en producción.';
            }
            if ((string) Config::get('mail.default') === 'log') {
                $issues[] = 'MAIL_MAILER=log no es recomendado para producción.';
            }
        }

        return $issues;
    }
}
