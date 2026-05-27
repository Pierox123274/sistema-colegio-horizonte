<?php

namespace App\Enums;

enum MeetingType: string
{
    case VirtualClass = 'virtual_class';
    case TeacherMeeting = 'teacher_meeting';
    case Tutoring = 'tutoring';
    case Advisory = 'advisory';
    case ParentMeeting = 'parent_meeting';
    case InstitutionalWebinar = 'institutional_webinar';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::VirtualClass->value, 'label' => 'Clase virtual'],
            ['value' => self::TeacherMeeting->value, 'label' => 'Reunión docente'],
            ['value' => self::Tutoring->value, 'label' => 'Tutoría'],
            ['value' => self::Advisory->value, 'label' => 'Asesoría'],
            ['value' => self::ParentMeeting->value, 'label' => 'Reunión con padres'],
            ['value' => self::InstitutionalWebinar->value, 'label' => 'Webinar institucional'],
        ];
    }
}
