<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\EducationalLevel;
use App\Enums\Gender;
use App\Enums\StudentStatus;
use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'first_name',
        'last_name',
        'document_type',
        'document_number',
        'birth_date',
        'gender',
        'educational_level',
        'grade',
        'section',
        'status',
        'address',
        'phone',
        'email',
        'medical_observations',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'document_type' => DocumentType::class,
            'educational_level' => EducationalLevel::class,
            'gender' => Gender::class,
            'status' => StudentStatus::class,
        ];
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * @return BelongsToMany<Guardian, $this>
     */
    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'guardian_student')
            ->withPivot([
                'relationship',
                'is_primary',
                'is_financial_responsible',
                'emergency_priority',
                'observations',
            ])
            ->withTimestamps();
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return HasMany<Sale, $this>
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
