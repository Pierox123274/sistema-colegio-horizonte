<?php

namespace App\Enums;

enum OnlineExamAttemptStatus: string
{
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
