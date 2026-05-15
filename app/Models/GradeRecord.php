<?php

namespace App\Models;

use Database\Factories\GradeRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeRecord extends Model
{
    /** @use HasFactory<GradeRecordFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'evaluation_id',
        'student_id',
        'score',
        'observations',
        'recorded_by_user_id',
    ];

    /**
     * @return array<string,string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Evaluation, $this>
     */
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }
}
