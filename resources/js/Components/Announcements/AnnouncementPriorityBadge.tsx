import { AlertTriangle, Bell, Info, Megaphone } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

const CONFIG: Record<string, { label: string; className: string; icon: LucideIcon }> = {
    baja: {
        label: 'Baja',
        className: 'bg-slate-100 text-slate-700 ring-slate-200',
        icon: Info,
    },
    media: {
        label: 'Media',
        className: 'bg-blue-100 text-blue-800 ring-blue-200',
        icon: Bell,
    },
    alta: {
        label: 'Alta',
        className: 'bg-orange-100 text-orange-900 ring-orange-200',
        icon: AlertTriangle,
    },
    urgente: {
        label: 'Urgente',
        className: 'bg-red-100 text-red-800 ring-red-200',
        icon: Megaphone,
    },
};

export default function AnnouncementPriorityBadge({
    priority,
    label,
}: {
    priority: string;
    label?: string;
}) {
    const config = CONFIG[priority] ?? CONFIG.media;
    const Icon = config.icon;

    return (
        <span
            className={`inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset ${config.className}`}
        >
            <Icon className="h-3.5 w-3.5" aria-hidden />
            {label ?? config.label}
        </span>
    );
}
