import type { LucideIcon } from 'lucide-react';
import type { ReactNode } from 'react';

type EmptyStateProps = {
    icon: LucideIcon;
    title: string;
    description?: string;
    action?: ReactNode;
};

export function EmptyState({
    icon: Icon,
    title,
    description,
    action,
}: EmptyStateProps) {
    return (
        <div className="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300/80 bg-slate-50/80 px-6 py-12 text-center dark:border-white/15 dark:bg-slate-900/40">
            <div className="flex h-12 w-12 items-center justify-center rounded-full bg-navy-900/5 text-navy-900 dark:bg-white/10 dark:text-slate-200">
                <Icon className="h-6 w-6" strokeWidth={1.5} />
            </div>
            <h3 className="mt-4 text-base font-semibold text-navy-900 dark:text-slate-100">
                {title}
            </h3>
            {description && (
                <p className="mt-1 max-w-sm text-sm text-plomo dark:text-slate-400">
                    {description}
                </p>
            )}
            {action ? <div className="mt-6">{action}</div> : null}
        </div>
    );
}
