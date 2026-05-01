import { type LucideIcon } from 'lucide-react';
import { Card } from '@/Components/Intranet/Card';

type StatsCardProps = {
    title: string;
    value: string;
    subtitle?: string;
    icon: LucideIcon;
    trend?: { label: string; positive?: boolean };
    accent?: 'navy' | 'red' | 'yellow';
};

const accentRing = {
    navy: 'ring-navy-900/20 bg-navy-900/5 text-navy-900',
    red: 'ring-brand-red/25 bg-brand-red/10 text-brand-red',
    yellow: 'ring-brand-yellow/40 bg-brand-yellow/15 text-navy-900',
};

export function StatsCard({
    title,
    value,
    subtitle,
    icon: Icon,
    trend,
    accent = 'navy',
}: StatsCardProps) {
    return (
        <Card className="relative overflow-hidden">
            <div className="flex items-start justify-between gap-3">
                <div>
                    <p className="text-xs font-semibold uppercase tracking-wider text-plomo">
                        {title}
                    </p>
                    <p className="mt-2 text-2xl font-bold tracking-tight text-navy-900 sm:text-3xl">
                        {value}
                    </p>
                    {subtitle && (
                        <p className="mt-1 text-sm text-plomo">{subtitle}</p>
                    )}
                    {trend && (
                        <p
                            className={`mt-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ${
                                trend.positive !== false
                                    ? 'bg-emerald-50 text-emerald-800'
                                    : 'bg-rose-50 text-rose-800'
                            }`}
                        >
                            {trend.label}
                        </p>
                    )}
                </div>
                <div
                    className={`flex h-11 w-11 shrink-0 items-center justify-center rounded-xl ring-1 ${accentRing[accent]}`}
                >
                    <Icon className="h-5 w-5" strokeWidth={1.75} />
                </div>
            </div>
        </Card>
    );
}
