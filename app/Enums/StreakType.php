<?php

namespace App\Enums;

enum StreakType: string
{
    case DailyLogin = 'daily_login';
    case Study = 'study';
    case OnTimeSubmission = 'on_time_submission';
    case Attendance = 'attendance';
}
