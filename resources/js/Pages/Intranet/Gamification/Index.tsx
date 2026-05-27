import { AppCard } from '@/Components/App/AppCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { AppTable } from '@/Components/App/AppTable';
import { AppStatCard } from '@/Components/App/AppStatCard';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Activity, Bot, Gauge, Trophy } from 'lucide-react';

type Props = PageProps<{
    overview: {
        students_with_profile: number;
        avg_xp: number;
        avg_level: number;
        avg_engagement: number;
        xp_last_30d: number;
        ai_usage_last_30d: number;
        lms_activity_last_30d: number;
        top_students: Array<{
            student: string;
            code: string;
            level: number;
            xp: number;
        }>;
    };
}>;

export default function IntranetGamificationIndex() {
    const { overview } = usePage<Props>().props;

    return (
        <IntranetLayout title="Gamificación institucional">
            <Head title="Gamificación institucional" />
            <PageContainer>
                <AppPageHeader
                    title="Gamificación institucional"
                    description="Engagement, progreso y ranking saludable del ecosistema LMS, adaptive e IA."
                    actions={
                        <Link
                            href={route('intranet.analytics.index')}
                            className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900 app-transition hover:bg-slate-50 dark:hover:bg-slate-800"
                        >
                            Ver analítica general
                        </Link>
                    }
                    eyebrow="Fase 26"
                />

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <AppStatCard title="Perfiles gamificados" value={String(overview.students_with_profile)} icon={Trophy} />
                    <AppStatCard title="XP promedio" value={String(overview.avg_xp)} icon={Activity} />
                    <AppStatCard title="Nivel promedio" value={String(overview.avg_level)} icon={Gauge} />
                    <AppStatCard title="Engagement promedio" value={String(overview.avg_engagement)} icon={Bot} />
                </div>

                <div className="mt-6 grid gap-4 sm:grid-cols-3">
                    <AppCard>
                        <p className="text-xs uppercase tracking-wide text-plomo">XP (30 días)</p>
                        <p className="mt-1 text-2xl font-bold text-navy-900 dark:text-slate-100">{overview.xp_last_30d}</p>
                    </AppCard>
                    <AppCard>
                        <p className="text-xs uppercase tracking-wide text-plomo">Uso IA (30 días)</p>
                        <p className="mt-1 text-2xl font-bold text-navy-900 dark:text-slate-100">{overview.ai_usage_last_30d}</p>
                    </AppCard>
                    <AppCard>
                        <p className="text-xs uppercase tracking-wide text-plomo">Actividad LMS (30 días)</p>
                        <p className="mt-1 text-2xl font-bold text-navy-900 dark:text-slate-100">{overview.lms_activity_last_30d}</p>
                    </AppCard>
                </div>

                <div className="mt-6">
                    <AppTable title="Top estudiantes por XP" stickyHeader>
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b border-plomo/15 text-left text-xs uppercase text-plomo">
                                    <th className="px-3 py-2">Estudiante</th>
                                    <th className="px-3 py-2">Código</th>
                                    <th className="px-3 py-2">Nivel</th>
                                    <th className="px-3 py-2">XP</th>
                                </tr>
                            </thead>
                            <tbody>
                                {overview.top_students.map((row) => (
                                    <tr key={`${row.code}-${row.level}`} className="border-b border-plomo/10">
                                        <td className="px-3 py-2 font-medium text-navy-900 dark:text-slate-100">{row.student}</td>
                                        <td className="px-3 py-2 text-plomo">{row.code}</td>
                                        <td className="px-3 py-2">{row.level}</td>
                                        <td className="px-3 py-2 font-semibold">{row.xp}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </AppTable>
                </div>
            </PageContainer>
        </IntranetLayout>
    );
}

