<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationEmailLog extends Model
{
    protected $table = 'integration_email_logs';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'mailable_class',
        'recipient_hash',
        'subject',
        'status',
        'attempts',
        'mailer',
        'error_message',
        'sent_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }
}
