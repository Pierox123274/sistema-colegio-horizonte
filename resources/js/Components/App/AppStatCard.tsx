import { StatsCard } from '@/Components/Intranet/StatsCard';
import type { LucideIcon } from 'lucide-react';

type AppStatCardProps = {
    title: string;
    value: string;
    subtitle?: string;
    icon: LucideIcon;
    trend?: { label: string; positive?: boolean };
    accent?: 'navy' | 'red' | 'yellow';
};

export function AppStatCard(props: AppStatCardProps) {
    return <StatsCard {...props} />;
}
