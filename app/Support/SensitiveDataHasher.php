<?php

namespace App\Support;

final class SensitiveDataHasher
{
    public static function hashDocument(?string $documentNumber): ?string
    {
        if ($documentNumber === null) {
            return null;
        }

        $normalized = self::normalizeDocument($documentNumber);
        if ($normalized === '') {
            return null;
        }

        return hash_hmac('sha256', $normalized, (string) config('app.key'));
    }

    public static function normalizeDocument(string $documentNumber): string
    {
        return preg_replace('/\s+/', '', trim($documentNumber)) ?? '';
    }

    public static function looksLikeDocumentNumber(string $search): bool
    {
        $normalized = self::normalizeDocument($search);

        return $normalized !== '' && preg_match('/^[A-Za-z0-9\-]{4,32}$/', $normalized) === 1;
    }
}
