<?php

namespace App\Enums;

enum ChallengeType: string
{
    case TaskCompletion = 'task_completion';
    case AiUsage = 'ai_usage';
    case Assessment = 'assessment';
    case Attendance = 'attendance';
    case Adaptive = 'adaptive';
    case LmsParticipation = 'lms_participation';
}
