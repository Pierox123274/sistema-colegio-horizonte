<?php

namespace App\Models;

use Database\Factories\AnnouncementRecipientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementRecipient extends Model
{
    /** @use HasFactory<AnnouncementRecipientFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'announcement_id',
        'user_id',
    ];

    /**
     * @return BelongsTo<Announcement, $this>
     */
    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
