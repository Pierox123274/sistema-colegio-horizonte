import MeetingStatusBadge from '@/Components/Meetings/MeetingStatusBadge';
import { Link } from '@inertiajs/react';
import { Video } from 'lucide-react';

type Props = {
    title: string;
    timeLabel: string;
    status: string;
    href?: string;
    joinHref?: string;
    canJoin?: boolean;
};

export default function MeetingScheduleCard({
    title,
    timeLabel,
    status,
    href,
    joinHref,
    canJoin = false,
}: Props) {
    const content = (
        <div className="flex items-center gap-3 rounded-xl border border-plomo/10 bg-navy-50/40 px-3 py-3 dark:border-white/10 dark:bg-slate-800/50">
            <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-navy-900 text-white dark:bg-brand-yellow dark:text-navy-950">
                <Video className="h-5 w-5" />
            </span>
            <div className="min-w-0 flex-1">
                <p className="truncate text-sm font-semibold text-navy-900 dark:text-slate-100">{title}</p>
                <p className="text-xs text-plomo dark:text-slate-400">{timeLabel}</p>
            </div>
            <MeetingStatusBadge status={status} />
        </div>
    );

    if (canJoin && joinHref) {
        return (
            <a href={joinHref} className="block transition hover:opacity-90">
                {content}
            </a>
        );
    }

    if (href) {
        return (
            <Link href={href} className="block">
                {content}
            </Link>
        );
    }

    return content;
}
