<?php

namespace App\Models;

use App\Enums\PensionStatus;
use Database\Factories\PensionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pension extends Model
{
    /** @use HasFactory<PensionFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'enrollment_id',
        'payment_concept_id',
        'month',
        'year',
        'amount',
        'due_date',
        'status',
        'observations',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'status' => PensionStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Enrollment, $this>
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * @return BelongsTo<PaymentConcept, $this>
     */
    public function paymentConcept(): BelongsTo
    {
        return $this->belongsTo(PaymentConcept::class);
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
