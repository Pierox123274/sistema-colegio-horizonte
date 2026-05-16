<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\AITutorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateTeacherInsightsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $userId) {}

    public function handle(AITutorService $ai): void
    {
        $user = User::query()->find($this->userId);
        if ($user === null) {
            return;
        }
        $ai->warmTeacherCache($user);
    }
}
