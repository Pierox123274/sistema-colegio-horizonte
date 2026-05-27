import { AppCard } from '@/Components/App/AppCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import JoinMeetingButton from '@/Components/Meetings/JoinMeetingButton';
import MeetingStatusBadge from '@/Components/Meetings/MeetingStatusBadge';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, router } from '@inertiajs/react';

type Meeting = {
    id: number;
    title: string;
    description?: string | null;
    meeting_type_label?: string;
    provider_label?: string;
    status: string;
    scheduled_at_label?: string;
    duration_minutes?: number;
    can_join?: boolean;
    can_manage?: boolean;
    join_href?: string | null;
    waiting_room_enabled?: boolean;
    recording_allowed?: boolean;
    participants_count?: number;
    attendances_count?: number;
    classroom?: { title: string } | null;
};

type Props = PageProps<{
    meeting: Meeting;
}>;

export default function TeacherMeetingsShow({ meeting }: Props) {
    return (
        <TeacherLayout title={meeting.title}>
            <Head title={meeting.title} />
            <PageContainer width="default">
                <AppPageHeader
                    title={meeting.title}
                    description={meeting.meeting_type_label}
                    eyebrow="Videoclase"
                />

                <div className="mb-4 flex flex-wrap items-center gap-2">
                    <MeetingStatusBadge status={meeting.status} />
                    <span className="text-sm text-plomo dark:text-slate-400">
                        {meeting.scheduled_at_label} · {meeting.duration_minutes} min
                    </span>
                </div>

                <AppCard className="mb-6 space-y-4">
                    {meeting.description ? (
                        <p className="text-sm text-plomo dark:text-slate-300">{meeting.description}</p>
                    ) : null}
                    <p className="text-sm">
                        Proveedor: <strong>{meeting.provider_label}</strong>
                    </p>
                    {meeting.classroom ? (
                        <p className="text-sm">Aula: {meeting.classroom.title}</p>
                    ) : null}
                    <p className="text-sm text-plomo">
                        Participantes: {meeting.participants_count} · Asistencias registradas:{' '}
                        {meeting.attendances_count}
                    </p>

                    <div className="flex flex-wrap gap-3 pt-2">
                        {meeting.join_href ? (
                            <JoinMeetingButton
                                href={meeting.join_href}
                                disabled={!meeting.can_join}
                                label="Iniciar / Unirse"
                            />
                        ) : null}
                        {meeting.can_manage && meeting.status === 'scheduled' ? (
                            <>
                                <button
                                    type="button"
                                    className="min-h-11 rounded-xl border border-plomo/20 px-4 py-2 text-sm font-semibold dark:border-white/15"
                                    onClick={() =>
                                        router.post(route('teacher.meetings.start', meeting.id))
                                    }
                                >
                                    Marcar en curso
                                </button>
                                <button
                                    type="button"
                                    className="min-h-11 rounded-xl border border-red-200 px-4 py-2 text-sm font-semibold text-red-700"
                                    onClick={() => {
                                        if (confirm('¿Cancelar esta reunión?')) {
                                            router.post(
                                                route('teacher.meetings.cancel', meeting.id)
                                            );
                                        }
                                    }}
                                >
                                    Cancelar
                                </button>
                            </>
                        ) : null}
                    </div>
                </AppCard>
            </PageContainer>
        </TeacherLayout>
    );
}
