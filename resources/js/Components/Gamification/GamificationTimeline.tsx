import { History } from 'lucide-react';

type TimelineItem = {
    source: string;
    points: number;
    description: string;
    created_at?: string | null;
};

type GamificationTimelineProps = {
    items: TimelineItem[];
};

export default function GamificationTimeline({ items }: GamificationTimelineProps) {
    return (
        <div className="app-surface p-4">
            <div className="mb-3 flex items-center gap-2">
                <History className="h-4 w-4 text-navy-900 dark:text-slate-100" />
                <p className="text-sm font-semibold text-navy-900 dark:text-slate-100">Actividad reciente</p>
            </div>
            {items.length === 0 ? (
                <p className="text-sm text-plomo dark:text-slate-300">Sin actividad reciente.</p>
            ) : (
                <ul className="space-y-2">
                    {items.map((item, idx) => (
                        <li key={`${item.source}-${idx}`} className="rounded-lg border border-slate-200/70 p-3 dark:border-white/10">
                            <p className="text-sm font-medium text-navy-900 dark:text-slate-100">{item.description}</p>
                            <p className="text-xs text-plomo dark:text-slate-300">
                                +{item.points} XP · {item.created_at ? new Date(item.created_at).toLocaleString('es-PE') : '—'}
                            </p>
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
}

