<?php

namespace App\AI\DTO;

final readonly class AIChatResult
{
    /**
     * @param  array<string, mixed>|null  $rawMeta  Metadatos no persistentes (p. ej. uso API); no guardar PII.
     */
    public function __construct(
        public string $content,
        public bool $success,
        public string $model,
        public ?string $errorCode = null,
        public ?array $rawMeta = null,
    ) {}

    public static function failure(string $message, string $model = 'n/a', ?string $code = null): self
    {
        return new self($message, false, $model, $code);
    }

    public static function disabled(): self
    {
        return new self(
            'El tutor inteligente está deshabilitado en esta instalación. Un administrador puede habilitarlo cuando la institución configure el servicio de IA de forma segura.',
            false,
            'none',
            'disabled',
        );
    }
}
