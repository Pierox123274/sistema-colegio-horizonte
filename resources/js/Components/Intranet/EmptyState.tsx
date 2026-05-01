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
        <div className="flex flex-col items-center justify-center rounded-xl border border-dashed border-plomo/25 bg-white/80 px-6 py-12 text-center">
            <div className="flex h-12 w-12 items-center justify-center rounded-full bg-navy-900/5 text-navy-900">
                <Icon className="h-6 w-6" strokeWidth={1.5} />
            </div>
            <h3 className="mt-4 text-base font-semibold text-navy-900">{title}</h3>
            {description && (
                <p className="mt-1 max-w-sm text-sm text-plomo">{description}</p>
            )}
            {action ? <div className="mt-6">{action}</div> : null}
        </div>
    );
}
