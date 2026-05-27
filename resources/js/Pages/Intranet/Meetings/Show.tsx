import { AppCard } from '@/Components/App/AppCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import MeetingParticipantsList from '@/Components/Meetings/MeetingParticipantsList';
import MeetingStatusBadge from '@/Components/Meetings/MeetingStatusBadge';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head } from '@inertiajs/react';

type Props = PageProps<{
    meeting: Record<string, unknown>;
    participants: Array<{ id: number; name: string | null; role: string }>;
    attendances: Array<{ user: string | null; joined_at: string; duration_seconds: number | null }>;
}>;

export default function IntranetMeetingsShow({ meeting, participants, attendances }: Props) {
    const m = meeting as {
        title: string;
        status: string;
        scheduled_at_label?: string;
        provider_label?: string;
        host?: { name: string };
    };

    return (
        <IntranetLayout title={m.title}>
            <Head title={m.title} />
            <PageContainer width="default">
                <AppPageHeader title={m.title} eyebrow="Videoclase institucional" />
                <MeetingStatusBadge status={m.status} />
                <AppCard className="mt-4 mb-6">
                    <p className="text-sm text-plomo">
                        {m.scheduled_at_label} · {m.provider_label} · Anfitrión: {m.host?.name}
                    </p>
                </AppCard>
                <div className="grid gap-6 lg:grid-cols-2">
                    <AppCard title="Participantes">
                        <MeetingParticipantsList participants={participants} />
                    </AppCard>
                    <AppCard title="Asistencia virtual">
                        <ul className="space-y-2 text-sm">
                            {attendances.map((a, i) => (
                                <li key={i} className="flex justify-between border-b border-plomo/10 py-2">
                                    <span>{a.user}</span>
                                    <span className="text-plomo">{a.joined_at}</span>
                                </li>
                            ))}
                        </ul>
                    </AppCard>
                </div>
            </PageContainer>
        </IntranetLayout>
    );
}
