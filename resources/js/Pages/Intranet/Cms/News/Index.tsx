import { AppBadge } from '@/Components/App/AppBadge';
import { AppEmptyState } from '@/Components/App/AppEmptyState';
import { AppFilterBar } from '@/Components/App/AppFilterBar';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { AppTable } from '@/Components/App/AppTable';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, Link, router } from '@inertiajs/react';
import { Newspaper, Plus } from 'lucide-react';
import { FormEvent, useState } from 'react';

type NewsRow = {
    id: number;
    slug: string;
    title: string;
    status: string;
    is_featured: boolean;
    published_at: string | null;
    category?: { name: string } | null;
};

type Paginator = {
    data: NewsRow[];
    links: { url: string | null; label: string; active: boolean }[];
};

type Props = {
    news: Paginator;
    filters: { search?: string; status?: string };
    catalog: { statuses: { value: string; label: string }[] };
};

export default function CmsNewsIndex({ news, filters, catalog }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status ?? '');

    const apply = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('intranet.cms.news.index'),
            { search: search || undefined, status: status || undefined },
            { preserveState: true, replace: true },
        );
    };

    return (
        <IntranetLayout>
            <Head title="CMS — Noticias" />
            <PageContainer>
                <AppPageHeader
                    title="Noticias"
                    description="Comunicados y novedades del sitio público."
                    actions={
                        <Link
                            href={route('intranet.cms.news.create')}
                            className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white app-transition hover:bg-navy-800"
                        >
                            <Plus className="h-4 w-4" />
                            Nueva noticia
                        </Link>
                    }
                />

                <AppFilterBar className="mt-6">
                    <form onSubmit={apply} className="flex flex-wrap gap-3">
                        <input
                            type="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Buscar…"
                            className="rounded-lg border-slate-300 text-sm"
                        />
                        <select
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                            className="rounded-lg border-slate-300 text-sm"
                        >
                            <option value="">Todos los estados</option>
                            {catalog.statuses.map((s) => (
                                <option key={s.value} value={s.value}>
                                    {s.label}
                                </option>
                            ))}
                        </select>
                        <button
                            type="submit"
                            className="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium"
                        >
                            Filtrar
                        </button>
                    </form>
                </AppFilterBar>

                <div className="mt-6">
                <AppTable stickyHeader title="Listado de noticias">
                    {news.data.length === 0 ? (
                        <div className="p-4">
                            <AppEmptyState
                                icon={Newspaper}
                                title="Sin noticias registradas"
                                description="Crea la primera noticia para empezar a publicar contenido en la web."
                            />
                        </div>
                    ) : (
                    <table className="min-w-full text-sm">
                        <thead>
                            <tr className="border-b text-left text-plomo">
                                <th className="px-4 py-3">Título</th>
                                <th className="px-4 py-3">Categoría</th>
                                <th className="px-4 py-3">Estado</th>
                                <th className="px-4 py-3">Publicación</th>
                                <th className="px-4 py-3" />
                            </tr>
                        </thead>
                        <tbody>
                            {news.data.map((n) => (
                                <tr key={n.id} className="border-b border-slate-100">
                                    <td className="px-4 py-3 font-medium text-navy-900">
                                        {n.title}
                                        {n.is_featured ? (
                                            <span className="ml-2 align-middle">
                                                <AppBadge tone="warning">Destacada</AppBadge>
                                            </span>
                                        ) : null}
                                    </td>
                                    <td className="px-4 py-3 text-plomo">
                                        {n.category?.name ?? '—'}
                                    </td>
                                    <td className="px-4 py-3 capitalize">
                                        <AppBadge
                                            tone={
                                                n.status === 'published'
                                                    ? 'success'
                                                    : n.status === 'archived'
                                                      ? 'danger'
                                                      : 'neutral'
                                            }
                                        >
                                            {n.status}
                                        </AppBadge>
                                    </td>
                                    <td className="px-4 py-3 text-plomo">
                                        {n.published_at
                                            ? new Date(n.published_at).toLocaleDateString('es-PE')
                                            : '—'}
                                    </td>
                                    <td className="px-4 py-3 text-right">
                                        <Link
                                            href={route('intranet.cms.news.edit', n.id)}
                                            className="font-semibold text-navy-800 hover:underline"
                                        >
                                            Editar
                                        </Link>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                    )}
                </AppTable>
                </div>
            </PageContainer>
        </IntranetLayout>
    );
}
