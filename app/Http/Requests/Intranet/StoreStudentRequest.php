<?php

namespace App\Http\Requests\Intranet;

use App\Http\Requests\Intranet\Concerns\ValidatesStudentAttributes;
use App\Models\Student;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    use ValidatesStudentAttributes;

    public function authorize(): bool
    {
        return $this->user()?->can('create', Student::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->studentFieldRules();
    }
}
