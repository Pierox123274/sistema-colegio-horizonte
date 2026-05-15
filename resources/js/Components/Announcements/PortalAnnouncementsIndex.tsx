import AnnouncementCard from '@/Components/Announcements/AnnouncementCard';
import { Card } from '@/Components/Intranet/Card';
import { EmptyState } from '@/Components/Intranet/EmptyState';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import type { AnnouncementCardPayload, SelectOption } from '@/types';
import { router } from '@inertiajs/react';
import { Megaphone } from 'lucide-react';
import { FormEvent, useState } from 'react';

type Paginator = {
    data: AnnouncementCardPayload[];
    links: { url: string | null; label: string; active: boolean }[];
};

type Props = {
    title: string;
    announcements: Paginator;
    filters: { search: string; priority: string; unread_only: string };
    unread_count: number;
    catalog: { priorities: SelectOption[] };
};

export default function PortalAnnouncementsIndex({
    title,
    announcements,
    filters,
    unread_count,
    catalog,
}: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [priority, setPriority] = useState(filters.priority ?? '');
    const [unreadOnly, setUnreadOnly] = useState(filters.unread_only === '1');

    const apply = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            window.location.pathname,
            {
                search: search || undefined,
                priority: priority || undefined,
                unread_only: unreadOnly ? '1' : undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const rows = announcements.data ?? [];

    return (
        <PageContainer>
            <SectionTitle
                title={title}
                description={
                    unread_count > 0
                        ? `Tienes ${unread_count} comunicado(s) sin leer.`
                        : 'Avisos institucionales dirigidos a tu perfil.'
                }
            />

            <Card className="mb-6">
                <form onSubmit={apply} className="grid gap-4 md:grid-cols-4">
                    <label className="block text-sm md:col-span-2">
                        <span className="mb-1 block text-xs font-medium text-plomo">Buscar</span>
                        <input
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="w-full rounded-lg border border-plomo/25 px-3 py-2 text-sm"
                            placeholder="Título o contenido"
                        />
                    </label>
                    <label className="block text-sm">
                        <span className="mb-1 block text-xs font-medium text-plomo">Prioridad</span>
                        <select
                            value={priority}
                            onChange={(e) => setPriority(e.target.value)}
                            className="w-full rounded-lg border border-plomo/25 px-3 py-2 text-sm"
                        >
                            <option value="">Todas</option>
                            {catalog.priorities.map((p) => (
                                <option key={p.value} value={p.value}>
                                    {p.label}
                                </option>
                            ))}
                        </select>
                    </label>
                    <div className="flex flex-col justify-end gap-2">
                        <label className="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                checked={unreadOnly}
                                onChange={(e) => setUnreadOnly(e.target.checked)}
                            />
                            Solo no leídos
                        </label>
                        <button
                            type="submit"
                            className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                        >
                            Filtrar
                        </button>
                    </div>
                </form>
            </Card>

            {rows.length === 0 ? (
                <EmptyState
                    icon={Megaphone}
                    title="Sin comunicados"
                    description="No hay avisos que coincidan con los filtros."
                />
            ) : (
                <div className="space-y-4">
                    {rows.map((item) => (
                        <AnnouncementCard key={item.id} announcement={item} />
                    ))}
                </div>
            )}

            {announcements.links && announcements.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-2">
                    {announcements.links.map((link, i) =>
                        link.url ? (
                            <button
                                key={`${link.label}-${i}`}
                                type="button"
                                onClick={() => router.get(link.url!)}
                                className={`rounded px-3 py-1 text-sm ${
                                    link.active
                                        ? 'bg-navy-900 text-white'
                                        : 'bg-slate-100 text-navy-900'
                                }`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ) : null,
                    )}
                </div>
            )}
        </PageContainer>
    );
}
