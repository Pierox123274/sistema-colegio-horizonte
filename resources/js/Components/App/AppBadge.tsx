import type { PropsWithChildren } from 'react';

type Tone = 'neutral' | 'success' | 'warning' | 'danger' | 'info';

const toneClass: Record<Tone, string> = {
    neutral:
        'bg-slate-100 text-slate-700 ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700',
    success:
        'bg-emerald-50 text-emerald-800 ring-emerald-200 dark:bg-emerald-500/20 dark:text-emerald-200 dark:ring-emerald-700/40',
    warning:
        'bg-amber-50 text-amber-900 ring-amber-200 dark:bg-amber-500/20 dark:text-amber-200 dark:ring-amber-700/40',
    danger:
        'bg-rose-50 text-rose-800 ring-rose-200 dark:bg-rose-500/20 dark:text-rose-200 dark:ring-rose-700/40',
    info: 'bg-navy-50 text-navy-900 ring-navy-200 dark:bg-white/10 dark:text-slate-100 dark:ring-white/20',
};

export function AppBadge({
    children,
    tone = 'neutral',
}: PropsWithChildren<{ tone?: Tone }>) {
    return (
        <span
            className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset ${toneClass[tone]}`}
        >
            {children}
        </span>
    );
}
