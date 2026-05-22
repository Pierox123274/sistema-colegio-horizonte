<?php

namespace App\Enums;

enum AssignmentSubmissionStatus: string
{
    case Pending = 'pending';
    case Submitted = 'submitted';
    case Reviewed = 'reviewed';
    case Overdue = 'overdue';
}
