import NotificationBadge from '@/Components/Notifications/NotificationBadge';
import type { NotificationPayload, PageProps } from '@/types';
import { Link, router, usePage } from '@inertiajs/react';
import { Bell } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';

export default function NotificationDropdown() {
    const { notificationCenter } = usePage<PageProps>().props;
    const [open, setOpen] = useState(false);
    const panelRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const onClick = (e: MouseEvent) => {
            if (panelRef.current && !panelRef.current.contains(e.target as Node)) {
                setOpen(false);
            }
        };
        if (open) document.addEventListener('mousedown', onClick);

        return () => document.removeEventListener('mousedown', onClick);
    }, [open]);

    useEffect(() => {
        if (!open) {
            return;
        }

        const id = window.setInterval(() => {
            if (document.visibilityState !== 'visible') {
                return;
            }
            router.reload({ only: ['notificationCenter'] });
        }, 90000);

        return () => window.clearInterval(id);
    }, [open]);

    if (!notificationCenter) {
        return null;
    }

    const unread = notificationCenter.unread_count;
    const recent = notificationCenter.recent;

    return (
        <div className="relative" ref={panelRef}>
            <button
                type="button"
                className="relative flex h-10 w-10 items-center justify-center rounded-lg border border-plomo/10 text-plomo transition hover:border-navy-900/15 hover:bg-navy-50 hover:text-navy-900 dark:border-white/15 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-slate-100"
                onClick={() => setOpen((v) => !v)}
                aria-label={`Notificaciones${unread > 0 ? `, ${unread} sin leer` : ''}`}
            >
                <Bell className="h-5 w-5" strokeWidth={1.5} />
                {unread > 0 ? (
                    <span className="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-brand-red px-1 text-[10px] font-bold text-white">
                        {unread > 9 ? '9+' : unread}
                    </span>
                ) : null}
            </button>

            {open ? (
                <div className="absolute right-0 z-50 mt-2 w-[min(100vw-2rem,24rem)] rounded-xl border border-plomo/15 bg-white shadow-xl shadow-navy-900/10 dark:border-white/10 dark:bg-slate-900 dark:shadow-black/30">
                    <div className="flex items-center justify-between border-b border-plomo/10 px-4 py-3 dark:border-white/10">
                        <div>
                            <p className="text-sm font-semibold text-navy-900 dark:text-slate-100">Centro de notificaciones</p>
                            <p className="text-xs text-plomo dark:text-slate-400">{unread} sin leer</p>
                        </div>
                        <button
                            type="button"
                            className="text-xs font-semibold text-navy-900 hover:underline dark:text-brand-yellow"
                            onClick={() =>
                                router.post(route('notifications.read-all'), {}, { preserveScroll: true })
                            }
                        >
                            Marcar todas
                        </button>
                    </div>

                    <ul className="max-h-96 space-y-2 overflow-y-auto px-3 py-3">
                        {recent.length === 0 ? (
                            <li className="rounded-lg border border-dashed border-plomo/15 px-3 py-5 text-center text-sm text-plomo dark:border-white/15 dark:text-slate-400">
                                No tienes notificaciones recientes.
                            </li>
                        ) : (
                            recent.map((item: NotificationPayload) => (
                                <li key={item.id}>
                                    <div
                                        className={`rounded-lg border px-3 py-2 ${
                                            item.is_read
                                                ? 'border-plomo/10 bg-white dark:border-white/10 dark:bg-slate-900/60'
                                                : 'border-brand-yellow/30 bg-brand-yellow/5 dark:border-brand-yellow/40 dark:bg-brand-yellow/10'
                                        }`}
                                    >
                                        <div className="mb-1 flex items-center justify-between gap-2">
                                            <p className="text-[11px] font-semibold uppercase text-plomo">{item.category}</p>
                                            <NotificationBadge priority={item.priority} />
                                        </div>
                                        <p className="text-sm font-semibold text-navy-900 dark:text-slate-100">{item.title}</p>
                                        <p className="text-xs text-plomo dark:text-slate-400">{item.message}</p>
                                        <div className="mt-2 flex items-center justify-between">
                                            <p className="text-[11px] text-plomo">{item.created_at_label ?? 'Ahora'}</p>
                                            <div className="flex items-center gap-3">
                                                {!item.is_read ? (
                                                    <button
                                                        type="button"
                                                        className="text-[11px] font-semibold text-navy-900 hover:underline"
                                                        onClick={() =>
                                                            router.patch(
                                                                route('notifications.read', item.id),
                                                                {},
                                                                { preserveScroll: true }
                                                            )
                                                        }
                                                    >
                                                        Marcar leída
                                                    </button>
                                                ) : null}
                                                {item.action_url ? (
                                                    <Link
                                                        href={item.action_url}
                                                        className="text-[11px] font-semibold text-navy-900 hover:underline"
                                                        onClick={() => setOpen(false)}
                                                    >
                                                        Abrir
                                                    </Link>
                                                ) : null}
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            ))
                        )}
                    </ul>

                    <div className="border-t border-plomo/10 p-2 dark:border-white/10">
                        <Link
                            href={notificationCenter.center_href}
                            className="block rounded-lg px-3 py-2 text-center text-sm font-semibold text-navy-900 hover:bg-navy-50 dark:text-slate-100 dark:hover:bg-slate-800"
                            onClick={() => setOpen(false)}
                        >
                            Ver centro completo
                        </Link>
                    </div>
                </div>
            ) : null}
        </div>
    );
}
