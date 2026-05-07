<?php

namespace App\Models;

use App\Enums\PaymentConceptType;
use Database\Factories\PaymentConceptFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentConcept extends Model
{
    /** @use HasFactory<PaymentConceptFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'default_amount',
        'type',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'default_amount' => 'decimal:2',
            'is_active' => 'boolean',
            'type' => PaymentConceptType::class,
        ];
    }

    /**
     * @return HasMany<Pension, $this>
     */
    public function pensions(): HasMany
    {
        return $this->hasMany(Pension::class);
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
