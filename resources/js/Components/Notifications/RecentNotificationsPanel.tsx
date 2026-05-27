import NotificationCard from '@/Components/Notifications/NotificationCard';
import { Card } from '@/Components/Intranet/Card';
import type { NotificationPayload, PageProps } from '@/types';
import { Link, usePage } from '@inertiajs/react';

export default function RecentNotificationsPanel({ title = 'Notificaciones recientes' }: { title?: string }) {
    const { notificationCenter } = usePage<PageProps>().props;
    const recent = notificationCenter?.recent ?? [];
    const centerHref = notificationCenter?.center_href ?? '#';

    if (recent.length === 0) {
        return null;
    }

    return (
        <Card className="mb-8">
            <div className="mb-4 flex items-center justify-between gap-2">
                <div>
                    <h3 className="text-base font-semibold text-navy-900">{title}</h3>
                    <p className="text-sm text-plomo">{notificationCenter?.unread_count ?? 0} sin leer</p>
                </div>
                <Link href={centerHref} className="text-sm font-semibold text-navy-900 hover:underline">
                    Ver centro
                </Link>
            </div>
            <div className="space-y-3">
                {recent.slice(0, 3).map((item: NotificationPayload) => (
                    <NotificationCard key={item.id} item={item} />
                ))}
            </div>
        </Card>
    );
}
