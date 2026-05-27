<?php

namespace App\Enums;

enum ExperienceSource: string
{
    case AssignmentCompleted = 'assignment_completed';
    case AssignmentEarly = 'assignment_early';
    case ExamApproved = 'exam_approved';
    case ExamOutstanding = 'exam_outstanding';
    case AttendanceDaily = 'attendance_daily';
    case DiagnosticCompleted = 'diagnostic_completed';
    case AdaptiveImprovement = 'adaptive_improvement';
    case AiTutorUsage = 'ai_tutor_usage';
    case ChallengeCompleted = 'challenge_completed';
    case LmsParticipation = 'lms_participation';
}
