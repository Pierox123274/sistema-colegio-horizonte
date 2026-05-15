import AnnouncementCard from '@/Components/Announcements/AnnouncementCard';
import { Card } from '@/Components/Intranet/Card';
import type { AnnouncementCardPayload, PageProps } from '@/types';
import { Link, usePage } from '@inertiajs/react';

export default function RecentAnnouncementsPanel({
    title = 'Comunicados recientes',
}: {
    title?: string;
}) {
    const { announcementBell } = usePage<PageProps>().props;
    const recent = announcementBell?.recent ?? [];
    const indexHref = announcementBell?.index_href ?? '#';

    if (recent.length === 0) {
        return null;
    }

    return (
        <Card className="mb-8">
            <div className="mb-4 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h3 className="text-base font-semibold text-navy-900">{title}</h3>
                    {(announcementBell?.unread_count ?? 0) > 0 && (
                        <p className="text-sm text-plomo">
                            Tienes {announcementBell?.unread_count} comunicado(s) sin leer.
                        </p>
                    )}
                </div>
                <Link
                    href={indexHref}
                    className="text-sm font-semibold text-navy-900 hover:underline"
                >
                    Ver todos
                </Link>
            </div>
            <div className="space-y-3">
                {recent.slice(0, 3).map((item: AnnouncementCardPayload) => (
                    <AnnouncementCard key={item.id} announcement={item} />
                ))}
            </div>
        </Card>
    );
}
