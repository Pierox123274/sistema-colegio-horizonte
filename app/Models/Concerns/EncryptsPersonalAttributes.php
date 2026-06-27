<?php

namespace App\Models\Concerns;

use App\Support\SensitiveDataHasher;

trait EncryptsPersonalAttributes
{
    protected static function bootEncryptsPersonalAttributes(): void
    {
        static::saving(function (self $model): void {
            if (! $model->isDirty('document_number')) {
                return;
            }

            $document = $model->document_number;
            if ($document === null || $document === '') {
                $model->document_number_hash = null;

                return;
            }

            $model->document_number_hash = SensitiveDataHasher::hashDocument($document);
        });
    }
}
