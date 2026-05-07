<?php

namespace App\Http\Requests\Intranet;

use App\Models\AcademicYear;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var AcademicYear $year */
        $year = $this->route('academic_year');

        return $this->user()?->can('update', $year) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var AcademicYear $year */
        $year = $this->route('academic_year');

        return [
            'name' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1990', 'max:2100', Rule::unique('academic_years', 'year')->ignore($year->id)],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
