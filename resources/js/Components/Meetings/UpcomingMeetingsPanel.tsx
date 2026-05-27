import MeetingScheduleCard from '@/Components/Meetings/MeetingScheduleCard';
import { AppCard } from '@/Components/App/AppCard';
import type { MeetingCardData } from '@/Components/Meetings/MeetingCard';
import { Link } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';

type Props = {
    meetings: MeetingCardData[];
    indexHref: string;
    title?: string;
};

export default function UpcomingMeetingsPanel({
    meetings,
    indexHref,
    title = 'Próximas videoclases',
}: Props) {
    if (meetings.length === 0) {
        return null;
    }

    return (
        <AppCard className="mb-6">
            <div className="mb-4 flex items-center justify-between gap-2">
                <h2 className="text-sm font-bold uppercase tracking-wider text-navy-900 dark:text-slate-100">
                    {title}
                </h2>
                <Link
                    href={indexHref}
                    className="inline-flex items-center gap-1 text-xs font-semibold text-navy-900 hover:underline dark:text-brand-yellow"
                >
                    Ver todas
                    <ArrowRight className="h-3.5 w-3.5" />
                </Link>
            </div>
            <div className="space-y-2">
                {meetings.map((m) => (
                    <MeetingScheduleCard
                        key={m.id}
                        title={m.title}
                        timeLabel={m.scheduled_at_label ?? ''}
                        status={m.status}
                        href={m.show_href}
                        joinHref={m.can_join && m.join_href ? m.join_href : undefined}
                        canJoin={m.can_join}
                    />
                ))}
            </div>
        </AppCard>
    );
}
