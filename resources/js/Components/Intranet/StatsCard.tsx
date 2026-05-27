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
    navy: 'ring-navy-900/20 bg-navy-900/5 text-navy-900 dark:ring-white/20 dark:bg-white/10 dark:text-slate-100',
    red: 'ring-brand-red/25 bg-brand-red/10 text-brand-red dark:ring-brand-red/45 dark:bg-brand-red/20 dark:text-rose-200',
    yellow: 'ring-brand-yellow/40 bg-brand-yellow/15 text-navy-900 dark:ring-brand-yellow/50 dark:bg-brand-yellow/20 dark:text-amber-200',
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
                    <p className="text-xs font-semibold uppercase tracking-wider text-plomo dark:text-slate-400">
                        {title}
                    </p>
                    <p className="mt-2 text-2xl font-bold tracking-tight text-navy-900 sm:text-3xl dark:text-slate-100">
                        {value}
                    </p>
                    {subtitle && (
                        <p className="mt-1 text-sm text-plomo dark:text-slate-400">
                            {subtitle}
                        </p>
                    )}
                    {trend && (
                        <p
                            className={`mt-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ${
                                trend.positive !== false
                                    ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-200'
                                    : 'bg-rose-50 text-rose-800 dark:bg-rose-500/20 dark:text-rose-200'
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
