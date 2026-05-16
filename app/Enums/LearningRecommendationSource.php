<?php

namespace App\Enums;

enum LearningRecommendationSource: string
{
    case Diagnostic = 'diagnostic';
    case Grades = 'grades';
    case Attendance = 'attendance';
    case Rule = 'rule';
}
