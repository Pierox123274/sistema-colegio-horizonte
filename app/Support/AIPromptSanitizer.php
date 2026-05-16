<?php

namespace App\Support;

/** Sanitización básica anti inyección de prompt y límites de tamaño. */
final class AIPromptSanitizer
{
    private const BLOCK_PATTERNS = [
        '/ignore\s+(all\s+)?(previous|above)\s+instructions?/iu',
        '/\bsystem\s*:\s*/iu',
        '/\[\s*INST\s*\]/iu',
        '/<\s*script/i',
    ];

    public static function sanitizeUserMessage(string $raw, int $maxLength): string
    {
        $text = trim($raw);
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text) ?? $text;

        foreach (self::BLOCK_PATTERNS as $pattern) {
            if (preg_match($pattern, $text)) {
                $text = preg_replace($pattern, '[filtrado]', $text) ?? $text;
            }
        }

        if (mb_strlen($text) > $maxLength) {
            $text = mb_substr($text, 0, $maxLength).'…';
        }

        return $text;
    }

    public static function hashPayload(string ...$parts): string
    {
        return hash('sha256', implode('|', $parts));
    }
}
