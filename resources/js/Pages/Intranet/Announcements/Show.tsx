import AnnouncementPriorityBadge from '@/Components/Announcements/AnnouncementPriorityBadge';
import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';

type Props = PageProps<{
    announcement: Record<string, unknown>;
    statistics: { audience_size: number; read_count: number; unread_count: number; read_percentage: number };
    can_manage: boolean;
}>;

export default function AnnouncementsAdminShow() {
    const { announcement, statistics, can_manage } = usePage<Props>().props;
    const id = announcement.id as number;

    return (
        <IntranetLayout title="Detalle comunicado">
            <Head title={String(announcement.title)} />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Comunicados', href: route('intranet.announcements.index') },
                        { label: 'Detalle' },
                    ]}
                />
                <SectionTitle
                    title={String(announcement.title)}
                    actions={
                        can_manage ? (
                            <div className="flex flex-wrap gap-2">
                                <Link
                                    href={route('intranet.announcements.edit', id)}
                                    className="rounded-lg border border-plomo/20 px-3 py-2 text-sm font-semibold"
                                >
                                    Editar
                                </Link>
                                <button
                                    type="button"
                                    onClick={() =>
                                        router.post(route('intranet.announcements.resend', id))
                                    }
                                    className="rounded-lg border border-plomo/20 px-3 py-2 text-sm font-semibold"
                                >
                                    Reenviar
                                </button>
                                <button
                                    type="button"
                                    onClick={() =>
                                        router.post(route('intranet.announcements.deactivate', id))
                                    }
                                    className="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-900"
                                >
                                    Desactivar
                                </button>
                            </div>
                        ) : undefined
                    }
                />
                <div className="mb-6 grid gap-4 sm:grid-cols-4">
                    <Card>
                        <p className="text-xs uppercase text-plomo">Audiencia</p>
                        <p className="text-2xl font-bold">{statistics.audience_size}</p>
                    </Card>
                    <Card>
                        <p className="text-xs uppercase text-plomo">Leídos</p>
                        <p className="text-2xl font-bold">{statistics.read_count}</p>
                    </Card>
                    <Card>
                        <p className="text-xs uppercase text-plomo">Sin leer</p>
                        <p className="text-2xl font-bold">{statistics.unread_count}</p>
                    </Card>
                    <Card>
                        <p className="text-xs uppercase text-plomo">% lectura</p>
                        <p className="text-2xl font-bold">{statistics.read_percentage}%</p>
                    </Card>
                </div>
                <Card>
                    <AnnouncementPriorityBadge priority={String(announcement.priority)} />
                    <div
                        className="prose prose-sm mt-4 max-w-none"
                        dangerouslySetInnerHTML={{
                            __html: String(announcement.content).replace(/\n/g, '<br />'),
                        }}
                    />
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
