<?php

namespace App\Jobs;

use App\Mail\NewAnnouncementPublishedMail;
use App\Models\Announcement;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class NotifyAnnouncementPublishedJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $announcementId
    ) {}

    public function handle(): void
    {
        $to = config('devops.operations_email');
        if (! is_string($to) || $to === '') {
            return;
        }

        $announcement = Announcement::query()->find($this->announcementId);
        if ($announcement === null) {
            return;
        }

        Mail::to($to)->send(new NewAnnouncementPublishedMail($announcement));
    }
}
