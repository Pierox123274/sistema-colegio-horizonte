<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\EducationalLevel;
use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Models\Concerns\EncryptsPersonalAttributes;
use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use EncryptsPersonalAttributes, HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'code',
        'first_name',
        'last_name',
        'document_type',
        'document_number',
        'document_number_hash',
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
            'document_number' => 'encrypted',
            'address' => 'encrypted',
            'phone' => 'encrypted',
            'email' => 'encrypted',
            'medical_observations' => 'encrypted',
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
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    /**
     * @return HasMany<Attendance, $this>
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * @return HasMany<GradeRecord, $this>
     */
    public function gradeRecords(): HasMany
    {
        return $this->hasMany(GradeRecord::class);
    }

    /**
     * @return HasMany<DiagnosticAttempt, $this>
     */
    public function diagnosticAttempts(): HasMany
    {
        return $this->hasMany(DiagnosticAttempt::class);
    }

    /**
     * @return HasOne<StudentAdaptiveProfile, $this>
     */
    public function adaptiveProfile(): HasOne
    {
        return $this->hasOne(StudentAdaptiveProfile::class);
    }

    /**
     * @return HasMany<LearningRecommendation, $this>
     */
    public function learningRecommendations(): HasMany
    {
        return $this->hasMany(LearningRecommendation::class);
    }

    /**
     * @return HasOne<GamificationProfile, $this>
     */
    public function gamificationProfile(): HasOne
    {
        return $this->hasOne(GamificationProfile::class);
    }

    /**
     * @return HasMany<ExperienceTransaction, $this>
     */
    public function experienceTransactions(): HasMany
    {
        return $this->hasMany(ExperienceTransaction::class);
    }

    /**
     * @return HasMany<StudentAchievement, $this>
     */
    public function achievementsUnlocked(): HasMany
    {
        return $this->hasMany(StudentAchievement::class);
    }

    /**
     * @return HasMany<StudentStreak, $this>
     */
    public function streaks(): HasMany
    {
        return $this->hasMany(StudentStreak::class);
    }

    /**
     * @return HasMany<StudentChallenge, $this>
     */
    public function challengesProgress(): HasMany
    {
        return $this->hasMany(StudentChallenge::class);
    }
}
