import { AlertTriangle, Info, ShieldAlert } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

const CONFIG: Record<string, { label: string; className: string; icon: LucideIcon }> = {
    info: {
        label: 'Info',
        className: 'bg-sky-50 text-sky-900 ring-sky-200',
        icon: Info,
    },
    warning: {
        label: 'Advertencia',
        className: 'bg-amber-50 text-amber-900 ring-amber-200',
        icon: AlertTriangle,
    },
    critical: {
        label: 'Crítico',
        className: 'bg-rose-50 text-rose-900 ring-rose-200',
        icon: ShieldAlert,
    },
};

export default function AuditSeverityBadge({ severity }: { severity: string }) {
    const key = severity || 'info';
    const config = CONFIG[key] ?? CONFIG.info;
    const Icon = config.icon;

    return (
        <span
            className={`inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset ${config.className}`}
        >
            <Icon className="h-3.5 w-3.5 shrink-0" aria-hidden />
            {config.label}
        </span>
    );
}
