import { CheckCircle2, Clock3, FileCheck, XCircle } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

const STATUS_CONFIG: Record<
    string,
    { label: string; className: string; icon: LucideIcon }
> = {
    presente: {
        label: 'Presente',
        className: 'bg-emerald-100 text-emerald-800 ring-emerald-200',
        icon: CheckCircle2,
    },
    tarde: {
        label: 'Tardanza',
        className: 'bg-amber-100 text-amber-800 ring-amber-200',
        icon: Clock3,
    },
    falta: {
        label: 'Falta',
        className: 'bg-red-100 text-red-800 ring-red-200',
        icon: XCircle,
    },
    justificado: {
        label: 'Justificado',
        className: 'bg-blue-100 text-blue-800 ring-blue-200',
        icon: FileCheck,
    },
};

export default function AttendanceStatusBadge({
    status,
    label,
}: {
    status: string;
    label?: string;
}) {
    const config = STATUS_CONFIG[status] ?? {
        label: label ?? status,
        className: 'bg-slate-100 text-slate-700 ring-slate-200',
        icon: FileCheck,
    };
    const Icon = config.icon;
    const text = label ?? config.label;

    return (
        <span
            className={`inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset ${config.className}`}
        >
            <Icon className="h-3.5 w-3.5" aria-hidden />
            {text}
        </span>
    );
}
