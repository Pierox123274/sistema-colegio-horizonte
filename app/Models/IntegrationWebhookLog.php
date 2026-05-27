<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationWebhookLog extends Model
{
    protected $table = 'integration_webhook_logs';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'provider',
        'event_type',
        'status',
        'signature_valid',
        'replay_count',
        'payload_summary',
        'error_message',
        'processed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload_summary' => 'array',
            'processed_at' => 'datetime',
        ];
    }
}
