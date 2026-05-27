import { AppCard } from '@/Components/App/AppCard';
import type { PropsWithChildren } from 'react';

export function AppFilterBar({
    children,
    className = '',
}: PropsWithChildren<{ className?: string }>) {
    return (
        <AppCard className={className} contentClassName="p-4">
            <div className="flex flex-wrap items-end gap-3">{children}</div>
        </AppCard>
    );
}
