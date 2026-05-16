<?php

namespace App\Enums;

enum DiagnosticAttemptStatus: string
{
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
