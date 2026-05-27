import { Target } from 'lucide-react';

type ChallengeCardProps = {
    title: string;
    description: string;
    progressValue: number;
    targetValue: number;
    progressPercent: number;
    xpReward: number;
    status: string;
};

export default function ChallengeCard({
    title,
    description,
    progressValue,
    targetValue,
    progressPercent,
    xpReward,
    status,
}: ChallengeCardProps) {
    return (
        <div className="app-surface p-4">
            <div className="mb-2 flex items-center justify-between gap-2">
                <div className="flex items-center gap-2">
                    <Target className="h-4 w-4 text-navy-900 dark:text-slate-100" />
                    <p className="text-sm font-semibold text-navy-900 dark:text-slate-100">{title}</p>
                </div>
                <span className="text-xs font-semibold text-brand-red">+{xpReward} XP</span>
            </div>
            <p className="text-xs text-plomo dark:text-slate-300">{description}</p>
            <div className="mt-3 h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                <div
                    className="h-full rounded-full bg-gradient-to-r from-navy-700 to-brand-red app-transition"
                    style={{ width: `${progressPercent}%` }}
                />
            </div>
            <div className="mt-2 flex items-center justify-between text-[11px] text-plomo dark:text-slate-300">
                <span>
                    {progressValue}/{targetValue}
                </span>
                <span className="capitalize">{status}</span>
            </div>
        </div>
    );
}

