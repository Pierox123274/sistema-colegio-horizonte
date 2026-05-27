/** Datos de demostración para el panel intranet (métricas y actividad). */

export const demoStats = [
    {
        title: 'Estudiantes activos',
        value: '1.248',
        subtitle: 'Inicial a secundaria',
        trend: { label: '+2,4% vs. mes anterior', positive: true as const },
        accent: 'navy' as const,
        iconKey: 'users',
    },
    {
        title: 'Matrículas (mes)',
        value: '94',
        subtitle: 'Procesos completados',
        trend: { label: '+12 nuevas esta semana', positive: true as const },
        accent: 'red' as const,
        iconKey: 'clipboard-list',
    },
    {
        title: 'Pensiones al día',
        value: '87%',
        subtitle: 'Cobranza mensual',
        trend: { label: 'Meta institucional 90%', positive: false as const },
        accent: 'yellow' as const,
        iconKey: 'wallet',
    },
    {
        title: 'Alertas de inventario',
        value: '3',
        subtitle: 'Stock bajo mínimo',
        trend: { label: 'Revisión sugerida', positive: false as const },
        accent: 'navy' as const,
        iconKey: 'package',
    },
];

export const demoActivity = [
    {
        id: '1',
        title: 'Comunicado: inicio de clases (demo)',
        time: 'Hace 25 min',
        tone: 'navy' as const,
    },
    {
        id: '2',
        title: 'Recordatorio: reunión de nivel secundaria (demo)',
        time: 'Hace 2 h',
        tone: 'red' as const,
    },
    {
        id: '3',
        title: 'Respaldo de datos completado (demo)',
        time: 'Ayer',
        tone: 'plomo' as const,
    },
];

export type QuickLink = {
    label: string;
    href?: string;
    disabled: boolean;
};

/** Accesos rápidos según rol (rutas reales cuando el módulo está activo). */
export function quickLinksForRoles(roles: string[]): QuickLink[] {
    if (roles.includes('Administrador')) {
        return [
            {
                label: 'Registrar pago',
                href: route('intranet.payments.create'),
                disabled: false,
            },
            {
                label: 'Estudiantes',
                href: route('intranet.students.index'),
                disabled: false,
            },
            {
                label: 'Centro de notificaciones',
                href: route('notifications.index'),
                disabled: false,
            },
            {
                label: 'Salud del sistema',
                href: route('intranet.system.health.index'),
                disabled: false,
            },
        ];
    }

    if (roles.includes('Secretaria')) {
        return [
            {
                label: 'Estudiantes',
                href: route('intranet.students.index'),
                disabled: false,
            },
            {
                label: 'Matrículas',
                href: route('intranet.enrollments.index'),
                disabled: false,
            },
            {
                label: 'Pagos',
                href: route('intranet.payments.index'),
                disabled: false,
            },
            {
                label: 'Notificaciones',
                href: route('notifications.index'),
                disabled: false,
            },
        ];
    }

    if (roles.includes('Docente')) {
        return [
            {
                label: 'Portal docente',
                href: route('teacher.dashboard'),
                disabled: false,
            },
            {
                label: 'Asistencia',
                href: route('teacher.attendance.index'),
                disabled: false,
            },
            {
                label: 'Notificaciones',
                href: route('notifications.index'),
                disabled: false,
            },
        ];
    }

    return [
        { label: 'Mi perfil', href: route('profile.edit'), disabled: false },
        {
            label: 'Notificaciones',
            href: route('notifications.index'),
            disabled: false,
        },
    ];
}
