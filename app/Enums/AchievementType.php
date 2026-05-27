<?php

namespace App\Enums;

enum AchievementType: string
{
    case Academic = 'academic';
    case Attendance = 'attendance';
    case Adaptive = 'adaptive';
    case Lms = 'lms';
    case Ai = 'ai';
    case Consistency = 'consistency';
}
