import { AppCard } from '@/Components/App/AppCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import JoinMeetingButton from '@/Components/Meetings/JoinMeetingButton';
import MeetingStatusBadge from '@/Components/Meetings/MeetingStatusBadge';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head } from '@inertiajs/react';

type Props = PageProps<{
    meeting: {
        id: number;
        title: string;
        description?: string | null;
        meeting_type_label?: string;
        status: string;
        scheduled_at_label?: string;
        duration_minutes?: number;
        can_join?: boolean;
        join_href?: string | null;
        classroom?: { title: string } | null;
    };
}>;

export default function StudentMeetingsShow({ meeting }: Props) {
    return (
        <StudentLayout title={meeting.title}>
            <Head title={meeting.title} />
            <PageContainer width="default">
                <AppPageHeader title={meeting.title} eyebrow="Videoclase" />
                <div className="mb-4">
                    <MeetingStatusBadge status={meeting.status} />
                </div>
                <AppCard className="space-y-4">
                    <p className="text-sm text-plomo dark:text-slate-300">
                        {meeting.scheduled_at_label} · {meeting.duration_minutes} min
                    </p>
                    {meeting.description ? (
                        <p className="text-sm">{meeting.description}</p>
                    ) : null}
                    {meeting.join_href ? (
                        <JoinMeetingButton
                            href={meeting.join_href}
                            disabled={!meeting.can_join}
                            className="w-full sm:w-auto"
                        />
                    ) : null}
                </AppCard>
            </PageContainer>
        </StudentLayout>
    );
}
