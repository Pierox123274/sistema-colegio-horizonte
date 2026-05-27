import { AppCard } from '@/Components/App/AppCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { AppStatCard } from '@/Components/App/AppStatCard';
import MeetingCard, { type MeetingCardData } from '@/Components/Meetings/MeetingCard';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head } from '@inertiajs/react';
import { CalendarCheck, Users, Video } from 'lucide-react';

type Props = PageProps<{
    meetings: {
        data: MeetingCardData[];
    };
    metrics: {
        total: number;
        scheduled: number;
        live: number;
        completed: number;
        attendances: number;
        avg_attendees: number;
    };
}>;

export default function IntranetMeetingsIndex({ meetings, metrics }: Props) {
    return (
        <IntranetLayout title="Videoclases institucionales">
            <Head title="Videoclases — Administración" />
            <PageContainer>
                <AppPageHeader
                    title="Videoclases y reuniones"
                    description="Métricas de uso, engagement y sesiones activas en la plataforma."
                    eyebrow="LMS · Comunicación"
                />

                <div className="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <AppStatCard title="Total reuniones" value={String(metrics.total)} icon={Video} />
                    <AppStatCard title="Programadas" value={String(metrics.scheduled)} accent="navy" icon={CalendarCheck} />
                    <AppStatCard title="En curso" value={String(metrics.live)} accent="yellow" icon={Video} />
                    <AppStatCard
                        title="Asistencia media"
                        value={String(metrics.avg_attendees)}
                        subtitle={`${metrics.attendances} registros`}
                        icon={Users}
                    />
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    {meetings.data.map((m) => (
                        <MeetingCard key={m.id} meeting={m} />
                    ))}
                </div>

                {meetings.data.length === 0 ? (
                    <AppCard className="mt-4">
                        <p className="text-sm text-plomo">Aún no hay videoclases registradas.</p>
                    </AppCard>
                ) : null}
            </PageContainer>
        </IntranetLayout>
    );
}
