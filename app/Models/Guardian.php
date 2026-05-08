<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\GuardianRelationshipType;
use Database\Factories\GuardianFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guardian extends Model
{
    /** @use HasFactory<GuardianFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'document_type',
        'document_number',
        'relationship_type',
        'phone',
        'secondary_phone',
        'email',
        'occupation',
        'address',
        'workplace',
        'is_emergency_contact',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'document_type' => DocumentType::class,
            'relationship_type' => GuardianRelationshipType::class,
            'is_emergency_contact' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<Student, $this>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'guardian_student')
            ->withPivot([
                'relationship',
                'is_primary',
                'is_financial_responsible',
                'emergency_priority',
                'observations',
            ])
            ->withTimestamps();
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
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
