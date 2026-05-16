<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Placeholder para futuras exportaciones analíticas pesadas en cola.
 */
class ProcessAnalyticsExportJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        public array $meta = []
    ) {}

    public function handle(): void
    {
        Log::info('ProcessAnalyticsExportJob (reservado para IA / exportaciones asíncronas).', $this->meta);
    }
}
