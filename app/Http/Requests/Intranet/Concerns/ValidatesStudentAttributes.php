<?php

namespace App\Http\Requests\Intranet\Concerns;

use App\Enums\DocumentType;
use App\Enums\EducationalLevel;
use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Models\Student;
use App\Rules\UniqueDocumentNumber;
use App\Support\StudentGradeCatalog;
use Closure;
use Illuminate\Validation\Rule;

trait ValidatesStudentAttributes
{
    /**
     * @return array<string, mixed>
     */
    protected function studentFieldRules(?int $studentId = null): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:64',
                Rule::unique('students', 'code')->ignore($studentId),
            ],
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'document_type' => ['required', Rule::enum(DocumentType::class)],
            'document_number' => [
                'nullable',
                'string',
                'max:32',
                new UniqueDocumentNumber(Student::class, $studentId),
            ],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'educational_level' => ['required', Rule::enum(EducationalLevel::class)],
            'grade' => ['required', 'string', 'max:32', $this->gradeValidator()],
            'section' => ['nullable', 'string', 'max:16'],
            'status' => ['required', Rule::enum(StudentStatus::class)],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'medical_observations' => ['nullable', 'string', 'max:5000'],
        ];
    }

    protected function gradeValidator(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! is_string($value)) {
                $fail('El grado no es válido.');

                return;
            }

            $levelValue = $this->input('educational_level');
            $level = is_string($levelValue) ? EducationalLevel::tryFrom($levelValue) : null;

            if ($level === null) {
                return;
            }

            $allowed = StudentGradeCatalog::gradesFor($level);
            if (! in_array($value, $allowed, true)) {
                $fail('El grado no es válido para el nivel educativo seleccionado.');
            }
        };
    }
}
