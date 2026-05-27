import AchievementBadge from '@/Components/Gamification/AchievementBadge';
import ChallengeCard from '@/Components/Gamification/ChallengeCard';
import GamificationTimeline from '@/Components/Gamification/GamificationTimeline';
import LeaderboardCard from '@/Components/Gamification/LeaderboardCard';
import StreakCard from '@/Components/Gamification/StreakCard';
import XPProgressCard from '@/Components/Gamification/XPProgressCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type Props = PageProps<{
    summary: {
        profile: {
            total_xp: number;
            current_level: number;
            next_level: number;
            xp_to_next_level: number;
            progress_percent: number;
            engagement_score: number;
        };
        achievements: Array<{
            code: string;
            title: string;
            description: string;
            rarity: string;
            unlocked_at?: string | null;
        }>;
        streaks: {
            study: { current: number; best: number };
            attendance: { current: number; best: number };
            on_time_submission: { current: number; best: number };
        };
        challenges: Array<{
            title: string;
            description: string;
            progress_value: number;
            target_value: number;
            progress_percent: number;
            xp_reward: number;
            status: string;
        }>;
        ranking: { position: number | null; total_students: number };
        recent_activity: Array<{
            source: string;
            points: number;
            description: string;
            created_at?: string | null;
        }>;
    };
}>;

export default function StudentGamificationIndex() {
    const { summary } = usePage<Props>().props;

    return (
        <StudentLayout title="Mi progreso gamificado">
            <Head title="Gamificación" />
            <PageContainer>
                <AppPageHeader
                    title="Mi progreso gamificado"
                    description="XP, niveles, logros, rachas y retos con enfoque pedagógico institucional."
                    eyebrow="Gamificación educativa"
                />

                <div className="grid gap-4 lg:grid-cols-3">
                    <div className="lg:col-span-2">
                        <XPProgressCard profile={summary.profile} />
                    </div>
                    <LeaderboardCard
                        position={summary.ranking.position}
                        totalStudents={summary.ranking.total_students}
                    />
                </div>

                <div className="mt-6 grid gap-4 md:grid-cols-3">
                    <StreakCard label="Racha de estudio" current={summary.streaks.study.current} best={summary.streaks.study.best} />
                    <StreakCard label="Racha de asistencia" current={summary.streaks.attendance.current} best={summary.streaks.attendance.best} />
                    <StreakCard
                        label="Entrega puntual"
                        current={summary.streaks.on_time_submission.current}
                        best={summary.streaks.on_time_submission.best}
                    />
                </div>

                <div className="mt-6 grid gap-4 lg:grid-cols-2">
                    <div className="app-surface p-5">
                        <h3 className="mb-3 text-sm font-bold uppercase tracking-wider text-navy-900 dark:text-slate-100">
                            Logros desbloqueados
                        </h3>
                        <div className="grid gap-3 sm:grid-cols-2">
                            {summary.achievements.length === 0 ? (
                                <p className="text-sm text-plomo dark:text-slate-300">Aún no hay logros desbloqueados.</p>
                            ) : (
                                summary.achievements.map((achievement) => (
                                    <AchievementBadge
                                        key={achievement.code}
                                        title={achievement.title}
                                        description={achievement.description}
                                        rarity={achievement.rarity}
                                        unlockedAt={achievement.unlocked_at}
                                    />
                                ))
                            )}
                        </div>
                    </div>
                    <div className="app-surface p-5">
                        <h3 className="mb-3 text-sm font-bold uppercase tracking-wider text-navy-900 dark:text-slate-100">
                            Retos activos
                        </h3>
                        <div className="space-y-3">
                            {summary.challenges.length === 0 ? (
                                <p className="text-sm text-plomo dark:text-slate-300">No hay retos activos por ahora.</p>
                            ) : (
                                summary.challenges.map((challenge, idx) => (
                                    <ChallengeCard
                                        key={`${challenge.title}-${idx}`}
                                        title={challenge.title}
                                        description={challenge.description}
                                        progressValue={challenge.progress_value}
                                        targetValue={challenge.target_value}
                                        progressPercent={challenge.progress_percent}
                                        xpReward={challenge.xp_reward}
                                        status={challenge.status}
                                    />
                                ))
                            )}
                        </div>
                    </div>
                </div>

                <div className="mt-6">
                    <GamificationTimeline items={summary.recent_activity} />
                </div>
            </PageContainer>
        </StudentLayout>
    );
}

