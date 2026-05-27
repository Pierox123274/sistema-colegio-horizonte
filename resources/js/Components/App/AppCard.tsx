import { Card } from '@/Components/Intranet/Card';
import type { PropsWithChildren, ReactNode } from 'react';

type AppCardProps = PropsWithChildren<{
    title?: string;
    description?: string;
    action?: ReactNode;
    className?: string;
    contentClassName?: string;
}>;

export function AppCard({
    title,
    description,
    action,
    className = '',
    contentClassName = '',
    children,
}: AppCardProps) {
    return (
        <Card className={`overflow-hidden ${className}`} padding="none">
            {(title || description || action) && (
                <div className="border-b border-slate-200/80 px-5 py-4 dark:border-white/10">
                    <div className="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            {title ? (
                                <h3 className="text-sm font-semibold text-navy-900 dark:text-slate-100">
                                    {title}
                                </h3>
                            ) : null}
                            {description ? (
                                <p className="mt-0.5 text-xs text-plomo dark:text-slate-400">
                                    {description}
                                </p>
                            ) : null}
                        </div>
                        {action}
                    </div>
                </div>
            )}
            <div className={`p-5 sm:p-6 ${contentClassName}`}>{children}</div>
        </Card>
    );
}
