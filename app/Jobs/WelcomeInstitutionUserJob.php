<?php

namespace App\Jobs;

use App\Mail\WelcomeInstitutionUserMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class WelcomeInstitutionUserJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $userId
    ) {}

    public function handle(): void
    {
        $user = User::query()->find($this->userId);
        if ($user === null) {
            return;
        }

        Mail::to($user->email)->send(new WelcomeInstitutionUserMail($user));
    }
}
