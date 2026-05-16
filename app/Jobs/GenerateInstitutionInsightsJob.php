<?php

namespace App\Jobs;

use App\Services\AITutorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateInstitutionInsightsJob implements ShouldQueue
{
    use Queueable;

    public function handle(AITutorService $ai): void
    {
        $ai->warmInstitutionCache();
    }
}
