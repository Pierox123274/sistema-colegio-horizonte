import MeetingStatusBadge from '@/Components/Meetings/MeetingStatusBadge';
import JoinMeetingButton from '@/Components/Meetings/JoinMeetingButton';
import { Link } from '@inertiajs/react';
import { Calendar, Clock } from 'lucide-react';

export type MeetingCardData = {
    id: number;
    title: string;
    meeting_type_label?: string;
    provider_label?: string;
    status: string;
    scheduled_at_label?: string;
    duration_minutes?: number;
    can_join?: boolean;
    classroom?: { title: string } | null;
    show_href?: string;
    join_href?: string | null;
};

type Props = {
    meeting: MeetingCardData;
};

export default function MeetingCard({ meeting }: Props) {
    return (
        <article className="rounded-2xl border border-plomo/15 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-slate-900/85">
            <div className="mb-3 flex items-start justify-between gap-2">
                <div className="min-w-0">
                    <p className="text-[11px] font-semibold uppercase tracking-wide text-brand-red">
                        {meeting.meeting_type_label ?? 'Videoclase'}
                    </p>
                    <h3 className="truncate text-base font-bold text-navy-900 dark:text-slate-100">
                        {meeting.title}
                    </h3>
                </div>
                <MeetingStatusBadge status={meeting.status} />
            </div>

            <div className="mb-4 space-y-1 text-xs text-plomo dark:text-slate-400">
                <p className="flex items-center gap-1.5">
                    <Calendar className="h-3.5 w-3.5" />
                    {meeting.scheduled_at_label}
                </p>
                {meeting.duration_minutes ? (
                    <p className="flex items-center gap-1.5">
                        <Clock className="h-3.5 w-3.5" />
                        {meeting.duration_minutes} min · {meeting.provider_label}
                    </p>
                ) : null}
                {meeting.classroom ? (
                    <p className="truncate">Aula: {meeting.classroom.title}</p>
                ) : null}
            </div>

            <div className="flex flex-wrap gap-2">
                {meeting.join_href ? (
                    <JoinMeetingButton
                        href={meeting.join_href}
                        disabled={!meeting.can_join}
                        className="flex-1 sm:flex-none"
                    />
                ) : null}
                {meeting.show_href ? (
                    <Link
                        href={meeting.show_href}
                        className="inline-flex min-h-11 items-center rounded-xl border border-plomo/20 px-4 py-2.5 text-sm font-semibold text-navy-900 hover:bg-navy-50 dark:border-white/15 dark:text-slate-100 dark:hover:bg-slate-800"
                    >
                        Detalle
                    </Link>
                ) : null}
            </div>
        </article>
    );
}
