<?php

namespace App\Http\Requests\Intranet;

use App\Models\Grade;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Grade::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $levelId = $this->input('educational_level_id');

        return [
            'educational_level_id' => ['required', 'exists:educational_levels,id'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('grades', 'code')->where(fn ($q) => $q->where('educational_level_id', $levelId)),
            ],
            'name' => ['required', 'string', 'max:255'],
            'order' => [
                'required',
                'integer',
                'min:1',
                'max:32767',
                Rule::unique('grades', 'order')->where(fn ($q) => $q->where('educational_level_id', $levelId)),
            ],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
