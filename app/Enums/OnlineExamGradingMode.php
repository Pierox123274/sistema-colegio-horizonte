<?php

namespace App\Enums;

enum OnlineExamGradingMode: string
{
    case Automatic = 'automatic';
    case Manual = 'manual';
    case Mixed = 'mixed';
}
