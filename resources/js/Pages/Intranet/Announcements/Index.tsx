import AnnouncementPriorityBadge from '@/Components/Announcements/AnnouncementPriorityBadge';
import { AppBadge } from '@/Components/App/AppBadge';
import { AppFilterBar } from '@/Components/App/AppFilterBar';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { AppTable } from '@/Components/App/AppTable';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { FormEvent, useState } from 'react';

type Row = {
    id: number;
    title: string;
    priority: string;
    audience_type: string;
    starts_at: string;
    ends_at: string | null;
    is_active: boolean;
    reads_count?: number;
    created_by?: { name: string };
};

type Props = PageProps<{
    announcements: { data: Row[]; links: { url: string | null; label: string; active: boolean }[] };
    filters: Record<string, string>;
    catalog: { priorities: SelectOption[]; audiences: SelectOption[]; statuses: SelectOption[] };
}>;

export default function AnnouncementsAdminIndex() {
    const { announcements, filters, catalog } = usePage<Props>().props;
    const [search, setSearch] = useState(filters.search ?? '');
    const [priority, setPriority] = useState(filters.priority ?? '');
    const [status, setStatus] = useState(filters.status ?? '');

    const apply = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(route('intranet.announcements.index'), {
            search: search || undefined,
            priority: priority || undefined,
            status: status || undefined,
        });
    };

    return (
        <IntranetLayout title="Comunicados">
            <Head title="Comunicados — Administración" />
            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Comunicados' }]} />
                <AppPageHeader
                    title="Comunicados institucionales"
                    description="Crear, publicar y supervisar avisos por rol o destinatarios."
                    actions={
                        <Link
                            href={route('intranet.announcements.create')}
                            className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white app-transition hover:bg-navy-800"
                        >
                            <Plus className="h-4 w-4" />
                            Nuevo comunicado
                        </Link>
                    }
                />
                <AppFilterBar className="mb-6">
                    <form onSubmit={apply} className="grid gap-3 md:grid-cols-4">
                        <input
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Buscar..."
                            className="rounded-lg border border-plomo/25 px-3 py-2 text-sm md:col-span-2"
                        />
                        <select
                            value={priority}
                            onChange={(e) => setPriority(e.target.value)}
                            className="rounded-lg border border-plomo/25 px-3 py-2 text-sm"
                        >
                            <option value="">Prioridad</option>
                            {catalog.priorities.map((p) => (
                                <option key={p.value} value={p.value}>
                                    {p.label}
                                </option>
                            ))}
                        </select>
                        <button type="submit" className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white">
                            Filtrar
                        </button>
                    </form>
                </AppFilterBar>
                <AppTable stickyHeader title="Listado" description={`${announcements.data.length} en esta página`}>
                    <table className="min-w-full text-sm">
                        <thead>
                            <tr className="border-b border-plomo/15 text-left text-xs uppercase text-plomo">
                                <th className="px-3 py-2">Título</th>
                                <th className="px-3 py-2">Prioridad</th>
                                <th className="px-3 py-2">Audiencia</th>
                                <th className="px-3 py-2">Lecturas</th>
                                <th className="px-3 py-2">Estado</th>
                                <th className="px-3 py-2" />
                            </tr>
                        </thead>
                        <tbody>
                            {announcements.data.map((row) => (
                                <tr key={row.id} className="border-b border-plomo/10">
                                    <td className="px-3 py-2 font-medium">{row.title}</td>
                                    <td className="px-3 py-2">
                                        <AnnouncementPriorityBadge priority={row.priority} />
                                    </td>
                                    <td className="px-3 py-2 capitalize">{row.audience_type.replace('_', ' ')}</td>
                                    <td className="px-3 py-2">{row.reads_count ?? 0}</td>
                                    <td className="px-3 py-2">
                                        <AppBadge tone={row.is_active ? 'success' : 'neutral'}>
                                            {row.is_active ? 'Activo' : 'Inactivo'}
                                        </AppBadge>
                                    </td>
                                    <td className="px-3 py-2 text-right">
                                        <Link
                                            href={route('intranet.announcements.show', row.id)}
                                            className="font-semibold text-navy-900 hover:underline"
                                        >
                                            Ver
                                        </Link>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </AppTable>
            </PageContainer>
        </IntranetLayout>
    );
}
