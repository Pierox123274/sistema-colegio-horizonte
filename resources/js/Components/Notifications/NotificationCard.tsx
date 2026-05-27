import NotificationBadge from '@/Components/Notifications/NotificationBadge';
import type { NotificationPayload } from '@/types';
import { Link } from '@inertiajs/react';

type Props = {
    item: NotificationPayload;
};

export default function NotificationCard({ item }: Props) {
    const body = (
        <div
            className={`rounded-xl border px-3 py-3 transition hover:border-navy-200 hover:bg-navy-50/30 ${
                item.is_read ? 'border-plomo/15 bg-white' : 'border-brand-yellow/40 bg-brand-yellow/5'
            }`}
        >
            <div className="mb-1 flex items-center justify-between gap-2">
                <p className="text-xs font-semibold uppercase tracking-wide text-plomo">{item.category}</p>
                <NotificationBadge priority={item.priority} />
            </div>
            <p className="text-sm font-semibold text-navy-900">{item.title}</p>
            <p className="mt-1 text-xs text-plomo">{item.message}</p>
            <p className="mt-2 text-[11px] text-plomo">{item.created_at_label ?? 'Ahora'}</p>
        </div>
    );

    if (item.action_url) {
        return (
            <Link href={item.action_url} className="block">
                {body}
            </Link>
        );
    }

    return body;
}
