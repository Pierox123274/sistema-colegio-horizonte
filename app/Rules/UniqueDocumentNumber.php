<?php

namespace App\Rules;

use App\Support\SensitiveDataHasher;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

class UniqueDocumentNumber implements ValidationRule
{
    /**
     * @param  class-string<Model>  $modelClass
     */
    public function __construct(
        private readonly string $modelClass,
        private readonly ?int $ignoreId = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || trim($value) === '') {
            return;
        }

        $hash = SensitiveDataHasher::hashDocument($value);
        if ($hash === null) {
            return;
        }

        $exists = $this->modelClass::query()
            ->where('document_number_hash', $hash)
            ->when($this->ignoreId !== null, fn ($q) => $q->where('id', '!=', $this->ignoreId))
            ->exists();

        if ($exists) {
            $fail('El número de documento ya está registrado.');
        }
    }
}
