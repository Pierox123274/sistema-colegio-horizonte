<?php

namespace App\Enums;

enum AuditModule: string
{
    case Auth = 'auth';
    case Security = 'security';
    case Students = 'students';
    case Guardians = 'guardians';
    case Academic = 'academic';
    case Enrollment = 'enrollment';
    case Finance = 'finance';
    case Inventory = 'inventory';
    case Sales = 'sales';
    case Attendance = 'attendance';
    case Grades = 'grades';
    case Announcements = 'announcements';
    case Analytics = 'analytics';
    case Users = 'users';
    case Ai = 'ai';
    case AdaptiveLearning = 'adaptive_learning';
    case Lms = 'lms';
    case Cms = 'cms';
    case Gamification = 'gamification';
    case Notifications = 'notifications';
    case Meetings = 'meetings';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Auth->value, 'label' => 'Autenticación'],
            ['value' => self::Security->value, 'label' => 'Seguridad'],
            ['value' => self::Students->value, 'label' => 'Estudiantes'],
            ['value' => self::Guardians->value, 'label' => 'Apoderados'],
            ['value' => self::Academic->value, 'label' => 'Académico'],
            ['value' => self::Enrollment->value, 'label' => 'Matrículas'],
            ['value' => self::Finance->value, 'label' => 'Finanzas'],
            ['value' => self::Inventory->value, 'label' => 'Inventario'],
            ['value' => self::Sales->value, 'label' => 'Ventas'],
            ['value' => self::Attendance->value, 'label' => 'Asistencia'],
            ['value' => self::Grades->value, 'label' => 'Notas'],
            ['value' => self::Announcements->value, 'label' => 'Comunicados'],
            ['value' => self::Analytics->value, 'label' => 'Analítica'],
            ['value' => self::Users->value, 'label' => 'Usuarios'],
            ['value' => self::Ai->value, 'label' => 'IA institucional'],
            ['value' => self::AdaptiveLearning->value, 'label' => 'Aprendizaje adaptativo'],
            ['value' => self::Lms->value, 'label' => 'Aula virtual / LMS'],
            ['value' => self::Cms->value, 'label' => 'Sitio web / CMS'],
            ['value' => self::Gamification->value, 'label' => 'Gamificación educativa'],
            ['value' => self::Notifications->value, 'label' => 'Notificaciones'],
            ['value' => self::Meetings->value, 'label' => 'Videoclases y reuniones'],
        ];
    }
}
