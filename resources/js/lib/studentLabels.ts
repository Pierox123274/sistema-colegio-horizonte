/** Etiquetas de visualización alineadas a enums backend (App\Enums\*). */

export const EDUCATIONAL_LEVEL_LABELS: Record<string, string> = {
    inicial: 'Inicial',
    primaria: 'Primaria',
    secundaria: 'Secundaria',
};

export const STATUS_LABELS: Record<string, string> = {
    activo: 'Activo',
    inactivo: 'Inactivo',
    retirado: 'Retirado',
    egresado: 'Egresado',
};

export const DOCUMENT_TYPE_LABELS: Record<string, string> = {
    dni: 'DNI',
    ce: 'C.E.',
    pasaporte: 'Pasaporte',
    otro: 'Otro',
};

export const GENDER_LABELS: Record<string, string> = {
    masculino: 'Masculino',
    femenino: 'Femenino',
    otro: 'Otro',
};

export function levelBadgeClass(level: string): string {
    switch (level) {
        case 'inicial':
            return 'bg-sky-100 text-sky-900 ring-sky-200';
        case 'primaria':
            return 'bg-emerald-100 text-emerald-900 ring-emerald-200';
        case 'secundaria':
            return 'bg-violet-100 text-violet-900 ring-violet-200';
        default:
            return 'bg-plomo/15 text-navy-900 ring-plomo/20';
    }
}

export function statusBadgeClass(status: string): string {
    switch (status) {
        case 'activo':
            return 'bg-emerald-50 text-emerald-900 ring-emerald-200';
        case 'inactivo':
            return 'bg-plomo/15 text-navy-900 ring-plomo/25';
        case 'retirado':
            return 'bg-amber-50 text-amber-900 ring-amber-200';
        case 'egresado':
            return 'bg-navy-900/10 text-navy-900 ring-navy-900/15';
        default:
            return 'bg-plomo/15 text-navy-900 ring-plomo/20';
    }
}
