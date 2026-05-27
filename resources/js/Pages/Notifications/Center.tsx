import { AppCard } from '@/Components/App/AppCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import NotificationCenter from '@/Components/Notifications/NotificationCenter';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { NotificationPayload, PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';

type Props = PageProps<{
    notifications: NotificationPayload[];
    unread_count: number;
    filters: {
        category: string;
        status: string;
    };
    catalog: {
        categories: SelectOption[];
        statuses: SelectOption[];
    };
}>;

export default function NotificationsCenterPage() {
    const { notifications, unread_count, filters, catalog } = usePage<Props>().props;

    return (
        <IntranetLayout title="Centro de notificaciones">
            <Head title="Notificaciones — Horizonte" />
            <PageContainer>
                <AppPageHeader
                    title="Centro de notificaciones"
                    description="Comunicación operativa institucional: alertas académicas, financieras, LMS, seguridad y sistema."
                    eyebrow="Comunicación"
                />

                <AppCard className="mb-6">
                    <div className="grid gap-3 md:grid-cols-4">
                        <select
                            value={filters.category}
                            onChange={(e) =>
                                router.get(route('notifications.index'), { ...filters, category: e.target.value })
                            }
                            className="rounded-lg border border-plomo/20 bg-white px-3 py-2 text-sm"
                        >
                            <option value="">Todas las categorías</option>
                            {catalog.categories.map((item) => (
                                <option key={item.value} value={item.value}>
                                    {item.label}
                                </option>
                            ))}
                        </select>
                        <select
                            value={filters.status}
                            onChange={(e) =>
                                router.get(route('notifications.index'), { ...filters, status: e.target.value })
                            }
                            className="rounded-lg border border-plomo/20 bg-white px-3 py-2 text-sm"
                        >
                            {catalog.statuses.map((item) => (
                                <option key={item.value} value={item.value}>
                                    {item.label}
                                </option>
                            ))}
                        </select>
                        <button
                            type="button"
                            onClick={() => router.post(route('notifications.read-all'))}
                            className="rounded-lg border border-plomo/20 px-3 py-2 text-sm font-semibold text-navy-900 hover:bg-navy-50"
                        >
                            Marcar todas leídas
                        </button>
                        <Link
                            href={route('settings.notifications.edit')}
                            className="rounded-lg border border-plomo/20 px-3 py-2 text-center text-sm font-semibold text-navy-900 hover:bg-navy-50"
                        >
                            Preferencias
                        </Link>
                    </div>
                    <p className="mt-3 text-xs text-plomo">No leídas: {unread_count}</p>
                </AppCard>

                <NotificationCenter notifications={notifications} />
            </PageContainer>
        </IntranetLayout>
    );
}
