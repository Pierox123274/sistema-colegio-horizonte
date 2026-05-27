<?php

namespace App\Enums;

enum AcademicCalendarEventType: string
{
    case Assignment = 'assignment';
    case Exam = 'exam';
    case Event = 'event';
    case Reminder = 'reminder';
    case Meeting = 'meeting';
}
