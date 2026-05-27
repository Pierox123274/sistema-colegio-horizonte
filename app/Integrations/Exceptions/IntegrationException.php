<?php

namespace App\Integrations\Exceptions;

use Exception;

final class IntegrationException extends Exception
{
    public static function notConfigured(string $provider): self
    {
        return new self("Integración «{$provider}» no configurada en este entorno.");
    }

    public static function invalidSignature(string $provider): self
    {
        return new self("Firma de webhook inválida para «{$provider}».");
    }
}
