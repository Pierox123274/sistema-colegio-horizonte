/** Datos de demostración (Fase 3). Sin conexión a BD. */

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
        title: 'Actualización de lista de útiles (demo)',
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

export const demoQuickLinks = [
    { label: 'Nueva matrícula', disabled: true },
    { label: 'Registrar pago', disabled: true },
    { label: 'Consultar reportes', disabled: true },
    { label: 'Ajustes del sistema', disabled: true },
];
