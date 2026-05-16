<?php

namespace App\Jobs;

use App\Models\Student;
use App\Services\AITutorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateStudentInsightsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $studentId) {}

    public function handle(AITutorService $ai): void
    {
        $student = Student::query()->find($this->studentId);
        if ($student === null) {
            return;
        }
        $ai->warmStudentCache($student);
    }
}
