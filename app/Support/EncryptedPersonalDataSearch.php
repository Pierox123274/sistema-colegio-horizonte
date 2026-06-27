<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class EncryptedPersonalDataSearch
{
    /**
     * @param  Builder<Model>  $query
     */
    public static function applyDocumentOrTextSearch(Builder $query, string $search, array $textColumns): void
    {
        $like = '%'.$search.'%';

        $query->where(function (Builder $inner) use ($search, $like, $textColumns): void {
            foreach ($textColumns as $column) {
                $inner->orWhere($column, 'like', $like);
            }

            if (SensitiveDataHasher::looksLikeDocumentNumber($search)) {
                $hash = SensitiveDataHasher::hashDocument($search);
                if ($hash !== null) {
                    $inner->orWhere('document_number_hash', $hash);
                }
            }
        });
    }
}
