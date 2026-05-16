<?php

namespace App\Enums;

enum AuditAction: string
{
    case Login = 'login';
    case Logout = 'logout';
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';
    case Cancel = 'cancel';
    case Export = 'export';
    case PermissionChange = 'permission_change';
    case RoleChange = 'role_change';
    case CashOpen = 'cash_open';
    case CashClose = 'cash_close';
    case Sale = 'sale';
    case Payment = 'payment';
    case Enrollment = 'enrollment';
    case Attendance = 'attendance';
    case Grade = 'grade';
    case Announcement = 'announcement';
    case View = 'view';
    case AiQuery = 'ai_query';
    case Assessment = 'assessment';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Login->value, 'label' => 'Inicio de sesión'],
            ['value' => self::Logout->value, 'label' => 'Cierre de sesión'],
            ['value' => self::Create->value, 'label' => 'Creación'],
            ['value' => self::Update->value, 'label' => 'Edición'],
            ['value' => self::Delete->value, 'label' => 'Eliminación'],
            ['value' => self::Cancel->value, 'label' => 'Anulación'],
            ['value' => self::Export->value, 'label' => 'Exportación'],
            ['value' => self::PermissionChange->value, 'label' => 'Cambio de permisos'],
            ['value' => self::RoleChange->value, 'label' => 'Cambio de roles'],
            ['value' => self::CashOpen->value, 'label' => 'Apertura de caja'],
            ['value' => self::CashClose->value, 'label' => 'Cierre de caja'],
            ['value' => self::Sale->value, 'label' => 'Venta'],
            ['value' => self::Payment->value, 'label' => 'Pago'],
            ['value' => self::Enrollment->value, 'label' => 'Matrícula'],
            ['value' => self::Attendance->value, 'label' => 'Asistencia'],
            ['value' => self::Grade->value, 'label' => 'Nota'],
            ['value' => self::Announcement->value, 'label' => 'Comunicado'],
            ['value' => self::View->value, 'label' => 'Consulta'],
            ['value' => self::AiQuery->value, 'label' => 'Consulta IA'],
            ['value' => self::Assessment->value, 'label' => 'Evaluación / diagnóstico'],
        ];
    }
}
