<?php

namespace App\Models;

use Database\Factories\AssignmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    /** @use HasFactory<AssignmentFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'virtual_classroom_id',
        'title',
        'description',
        'max_score',
        'due_at',
        'rubric',
        'is_published',
        'created_by_user_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'max_score' => 'decimal:2',
            'due_at' => 'datetime',
            'rubric' => 'array',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<VirtualClassroom, $this>
     */
    public function virtualClassroom(): BelongsTo
    {
        return $this->belongsTo(VirtualClassroom::class);
    }

    /**
     * @return HasMany<AssignmentSubmission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
}
