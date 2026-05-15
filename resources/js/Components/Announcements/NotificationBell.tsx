import AnnouncementPriorityBadge from '@/Components/Announcements/AnnouncementPriorityBadge';
import type { AnnouncementCardPayload, PageProps } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { Bell } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';

export default function NotificationBell() {
    const { announcementBell } = usePage<PageProps>().props;
    const [open, setOpen] = useState(false);
    const panelRef = useRef<HTMLDivElement>(null);

    const unread = announcementBell?.unread_count ?? 0;
    const recent = announcementBell?.recent ?? [];
    const indexHref = announcementBell?.index_href ?? '#';

    useEffect(() => {
        const onClick = (e: MouseEvent) => {
            if (panelRef.current && !panelRef.current.contains(e.target as Node)) {
                setOpen(false);
            }
        };
        if (open) {
            document.addEventListener('mousedown', onClick);
        }
        return () => document.removeEventListener('mousedown', onClick);
    }, [open]);

    if (!announcementBell) {
        return null;
    }

    return (
        <div className="relative" ref={panelRef}>
            <button
                type="button"
                className="relative flex h-10 w-10 items-center justify-center rounded-lg border border-plomo/10 text-plomo transition hover:border-navy-900/15 hover:bg-navy-50 hover:text-navy-900"
                aria-label={`Comunicados${unread > 0 ? `, ${unread} sin leer` : ''}`}
                onClick={() => setOpen((v) => !v)}
            >
                <Bell className="h-5 w-5" strokeWidth={1.5} />
                {unread > 0 && (
                    <span className="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-brand-red px-1 text-[10px] font-bold text-white">
                        {unread > 9 ? '9+' : unread}
                    </span>
                )}
            </button>

            {open && (
                <div className="absolute right-0 z-50 mt-2 w-[min(100vw-2rem,22rem)] rounded-xl border border-plomo/15 bg-white shadow-xl shadow-navy-900/10">
                    <div className="border-b border-plomo/10 px-4 py-3">
                        <p className="text-sm font-semibold text-navy-900">Comunicados</p>
                        <p className="text-xs text-plomo">
                            {unread > 0
                                ? `${unread} sin leer`
                                : 'Estás al día con los avisos'}
                        </p>
                    </div>
                    <ul className="max-h-80 overflow-y-auto py-1">
                        {recent.length === 0 ? (
                            <li className="px-4 py-6 text-center text-sm text-plomo">
                                No hay comunicados recientes.
                            </li>
                        ) : (
                            recent.map((item: AnnouncementCardPayload) => (
                                <li key={item.id}>
                                    <Link
                                        href={item.show_href}
                                        className={`block px-4 py-3 hover:bg-navy-50/80 ${
                                            !item.is_read ? 'bg-brand-yellow/5' : ''
                                        }`}
                                        onClick={() => setOpen(false)}
                                    >
                                        <div className="mb-1 flex items-center justify-between gap-2">
                                            <AnnouncementPriorityBadge
                                                priority={item.priority}
                                                label={item.priority_label}
                                            />
                                            {!item.is_read && (
                                                <span className="h-2 w-2 shrink-0 rounded-full bg-brand-red" />
                                            )}
                                        </div>
                                        <p className="line-clamp-2 text-sm font-medium text-navy-900">
                                            {item.title}
                                        </p>
                                        <p className="mt-0.5 text-xs text-plomo">
                                            {item.starts_at_label}
                                        </p>
                                    </Link>
                                </li>
                            ))
                        )}
                    </ul>
                    <div className="border-t border-plomo/10 p-2">
                        <Link
                            href={indexHref}
                            className="block rounded-lg px-3 py-2 text-center text-sm font-semibold text-navy-900 hover:bg-navy-50"
                            onClick={() => setOpen(false)}
                        >
                            Ver todos los comunicados
                        </Link>
                    </div>
                </div>
            )}
        </div>
    );
}
