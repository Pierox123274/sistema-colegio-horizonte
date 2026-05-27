import { Award } from 'lucide-react';

type AchievementBadgeProps = {
    title: string;
    description: string;
    rarity: string;
    unlockedAt?: string | null;
};

export default function AchievementBadge({
    title,
    description,
    rarity,
    unlockedAt,
}: AchievementBadgeProps) {
    return (
        <div className="rounded-xl border border-slate-200/80 bg-white p-4 shadow-sm app-transition hover:shadow-md dark:border-white/10 dark:bg-slate-900/70">
            <div className="mb-2 flex items-center gap-2">
                <Award className="h-4 w-4 text-brand-yellow" />
                <p className="text-sm font-semibold text-navy-900 dark:text-slate-100">{title}</p>
            </div>
            <p className="text-xs text-plomo dark:text-slate-300">{description}</p>
            <div className="mt-3 flex items-center justify-between text-[11px]">
                <span className="rounded-full bg-slate-100 px-2 py-0.5 uppercase tracking-wide text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    {rarity}
                </span>
                <span className="text-plomo dark:text-slate-400">
                    {unlockedAt ? new Date(unlockedAt).toLocaleDateString('es-PE') : 'Bloqueado'}
                </span>
            </div>
        </div>
    );
}

