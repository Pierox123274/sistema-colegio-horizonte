type LevelProgressProps = {
    currentLevel: number;
    nextLevel: number;
    progressPercent: number;
    totalXp: number;
    xpToNextLevel: number;
};

export default function LevelProgress({
    currentLevel,
    nextLevel,
    progressPercent,
    totalXp,
    xpToNextLevel,
}: LevelProgressProps) {
    return (
        <div className="app-surface p-5">
            <div className="mb-3 flex items-center justify-between">
                <p className="text-sm font-semibold text-navy-900 dark:text-slate-100">Nivel {currentLevel}</p>
                <p className="text-xs text-plomo dark:text-slate-300">Siguiente: Nivel {nextLevel}</p>
            </div>
            <div className="h-2.5 w-full overflow-hidden rounded-full bg-slate-200/80 dark:bg-slate-800">
                <div
                    className="h-full rounded-full bg-gradient-to-r from-brand-yellow via-amber-400 to-brand-red app-transition"
                    style={{ width: `${progressPercent}%` }}
                />
            </div>
            <div className="mt-3 flex items-center justify-between text-xs text-plomo dark:text-slate-300">
                <span>{totalXp} XP</span>
                <span>{xpToNextLevel} XP para subir</span>
            </div>
        </div>
    );
}

