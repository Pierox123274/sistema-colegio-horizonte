<?php

namespace App\Http\Requests\Intranet;

use App\Http\Requests\Intranet\Concerns\ValidatesStudentAttributes;
use App\Models\Student;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    use ValidatesStudentAttributes;

    public function authorize(): bool
    {
        $student = $this->route('student');

        return $student instanceof Student
            && ($this->user()?->can('update', $student) ?? false);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $student = $this->route('student');
        if (! $student instanceof Student) {
            return [];
        }

        return $this->studentFieldRules($student->id);
    }
}
