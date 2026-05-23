<?php

namespace App\Models\Cms;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsMedia extends Model
{
    protected $table = 'cms_media';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'path',
        'disk',
        'filename',
        'mime',
        'size',
        'alt',
        'uploaded_by_user_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
