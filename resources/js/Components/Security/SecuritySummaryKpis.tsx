import AnalyticsKpiCard from '@/Components/Analytics/AnalyticsKpiCard';
import type { LucideIcon } from 'lucide-react';

export type SecurityKpiItem = {
    title: string;
    value: string | number;
    subtitle?: string;
    icon?: LucideIcon;
    accent?: 'navy' | 'yellow' | 'red';
};

export default function SecuritySummaryKpis({ items }: { items: SecurityKpiItem[] }) {
    if (items.length === 0) {
        return null;
    }

    return (
        <div className="mb-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
            {items.map((item) => (
                <AnalyticsKpiCard
                    key={item.title}
                    title={item.title}
                    value={item.value}
                    subtitle={item.subtitle}
                    icon={item.icon}
                    accent={item.accent}
                />
            ))}
        </div>
    );
}
