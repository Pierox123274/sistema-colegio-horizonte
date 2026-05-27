<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GamificationProfile extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'total_xp',
        'current_level',
        'xp_to_next_level',
        'engagement_score',
    ];

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
