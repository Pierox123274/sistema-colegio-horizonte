import AnnouncementPriorityBadge from '@/Components/Announcements/AnnouncementPriorityBadge';
import { Card } from '@/Components/Intranet/Card';
import type { AnnouncementCardPayload } from '@/types';
import { Link } from '@inertiajs/react';
import { Paperclip } from 'lucide-react';

export default function AnnouncementCard({
    announcement,
}: {
    announcement: AnnouncementCardPayload;
}) {
    return (
        <Card
            className={`transition hover:shadow-md ${
                announcement.is_read ? 'opacity-95' : 'border-l-4 border-l-brand-yellow'
            }`}
        >
            <div className="flex flex-wrap items-start justify-between gap-3">
                <div className="min-w-0 flex-1">
                    <div className="flex flex-wrap items-center gap-2">
                        <AnnouncementPriorityBadge
                            priority={announcement.priority}
                            label={announcement.priority_label}
                        />
                        {!announcement.is_read && (
                            <span className="rounded-full bg-brand-red px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">
                                Nuevo
                            </span>
                        )}
                        {announcement.has_attachment && (
                            <span className="inline-flex items-center gap-1 text-xs text-plomo">
                                <Paperclip className="h-3.5 w-3.5" />
                                Adjunto
                            </span>
                        )}
                    </div>
                    <h3 className="mt-2 text-base font-semibold text-navy-900">
                        {announcement.title}
                    </h3>
                    <p className="mt-1 line-clamp-2 text-sm text-plomo">
                        {announcement.content_excerpt}
                    </p>
                    <p className="mt-2 text-xs text-plomo">
                        {announcement.starts_at_label}
                        {announcement.created_by?.name
                            ? ` · ${announcement.created_by.name}`
                            : ''}
                    </p>
                </div>
                <Link
                    href={announcement.show_href}
                    className="shrink-0 rounded-lg border border-plomo/20 px-3 py-2 text-sm font-semibold text-navy-900 hover:bg-navy-50"
                >
                    Ver detalle
                </Link>
            </div>
        </Card>
    );
}
