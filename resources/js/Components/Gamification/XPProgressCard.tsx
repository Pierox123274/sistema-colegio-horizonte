import { Sparkles } from 'lucide-react';
import LevelProgress from './LevelProgress';

type XPProgressCardProps = {
    profile: {
        total_xp: number;
        current_level: number;
        next_level: number;
        xp_to_next_level: number;
        progress_percent: number;
        engagement_score: number;
    };
};

export default function XPProgressCard({ profile }: XPProgressCardProps) {
    return (
        <div className="app-surface bg-gradient-to-br from-white to-amber-50/70 p-5 dark:from-slate-900 dark:to-slate-900">
            <div className="mb-4 flex items-center gap-2">
                <Sparkles className="h-5 w-5 text-brand-red" />
                <h3 className="text-sm font-bold uppercase tracking-wider text-navy-900 dark:text-slate-100">
                    Progreso gamificado
                </h3>
            </div>
            <LevelProgress
                currentLevel={profile.current_level}
                nextLevel={profile.next_level}
                progressPercent={profile.progress_percent}
                totalXp={profile.total_xp}
                xpToNextLevel={profile.xp_to_next_level}
            />
            <p className="mt-3 text-xs text-plomo dark:text-slate-300">
                Engagement institucional: <span className="font-semibold">{profile.engagement_score}/100</span>
            </p>
        </div>
    );
}

