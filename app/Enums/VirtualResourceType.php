<?php

namespace App\Enums;

enum VirtualResourceType: string
{
    case Pdf = 'pdf';
    case Image = 'image';
    case Video = 'video';
    case Link = 'link';
    case Document = 'document';
}
