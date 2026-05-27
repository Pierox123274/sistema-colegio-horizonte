import NotificationCard from '@/Components/Notifications/NotificationCard';
import NotificationEmptyState from '@/Components/Notifications/NotificationEmptyState';
import type { NotificationPayload } from '@/types';

type Props = {
    notifications: NotificationPayload[];
};

export default function NotificationCenter({ notifications }: Props) {
    if (notifications.length === 0) {
        return <NotificationEmptyState />;
    }

    return (
        <div className="space-y-3">
            {notifications.map((item) => (
                <NotificationCard key={item.id} item={item} />
            ))}
        </div>
    );
}
