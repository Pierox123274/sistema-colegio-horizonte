import type { LucideIcon } from 'lucide-react';

type Props = {
    title: string;
    value: string | number;
    subtitle?: string;
    icon?: LucideIcon;
    accent?: 'navy' | 'yellow' | 'red';
};

const accentMap = {
    navy: 'border-l-navy-900',
    yellow: 'border-l-brand-yellow',
    red: 'border-l-brand-red',
};

export default function AnalyticsKpiCard({
    title,
    value,
    subtitle,
    icon: Icon,
    accent = 'navy',
}: Props) {
    return (
        <div className={`rounded-xl border border-plomo/15 bg-white p-4 shadow-sm border-l-4 ${accentMap[accent]}`}>
            <div className="flex items-start justify-between gap-2">
                <div>
                    <p className="text-xs font-semibold uppercase tracking-wide text-plomo">{title}</p>
                    <p className="mt-1 text-2xl font-bold text-navy-900">{value}</p>
                    {subtitle && <p className="mt-1 text-sm text-plomo">{subtitle}</p>}
                </div>
                {Icon && (
                    <span className="rounded-lg bg-navy-50 p-2 text-navy-900">
                        <Icon className="h-5 w-5" strokeWidth={1.75} />
                    </span>
                )}
            </div>
        </div>
    );
}
