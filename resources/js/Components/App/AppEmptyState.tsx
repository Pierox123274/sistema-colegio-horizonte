import { EmptyState } from '@/Components/Intranet/EmptyState';
import type { LucideIcon } from 'lucide-react';
import type { ReactNode } from 'react';

type AppEmptyStateProps = {
    icon: LucideIcon;
    title: string;
    description?: string;
    action?: ReactNode;
};

export function AppEmptyState(props: AppEmptyStateProps) {
    return <EmptyState {...props} />;
}
