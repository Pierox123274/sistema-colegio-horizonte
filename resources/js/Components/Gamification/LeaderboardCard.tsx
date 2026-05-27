import { Trophy } from 'lucide-react';

type LeaderboardCardProps = {
    position: number | null;
    totalStudents: number;
};

export default function LeaderboardCard({ position, totalStudents }: LeaderboardCardProps) {
    return (
        <div className="app-surface p-4">
            <div className="mb-2 flex items-center gap-2">
                <Trophy className="h-4 w-4 text-brand-yellow" />
                <p className="text-sm font-semibold text-navy-900 dark:text-slate-100">Ranking saludable</p>
            </div>
            <p className="text-2xl font-bold text-navy-900 dark:text-slate-100">
                {position ? `#${position}` : '—'}
            </p>
            <p className="text-xs text-plomo dark:text-slate-300">de {totalStudents} estudiantes</p>
        </div>
    );
}

