import { Flame } from 'lucide-react';

type StreakCardProps = {
    label: string;
    current: number;
    best: number;
};

export default function StreakCard({ label, current, best }: StreakCardProps) {
    return (
        <div className="app-surface p-4">
            <div className="mb-2 flex items-center gap-2">
                <Flame className="h-4 w-4 text-brand-red" />
                <p className="text-sm font-semibold text-navy-900 dark:text-slate-100">{label}</p>
            </div>
            <div className="flex items-end justify-between">
                <p className="text-2xl font-bold text-navy-900 dark:text-slate-100">{current}</p>
                <p className="text-xs text-plomo dark:text-slate-300">Mejor: {best}</p>
            </div>
        </div>
    );
}

