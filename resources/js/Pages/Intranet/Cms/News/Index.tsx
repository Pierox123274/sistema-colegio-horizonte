import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, Link, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
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
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <SectionTitle title="Noticias" description="Comunicados y novedades del sitio público." />
                    <Link
                        href={route('intranet.cms.news.create')}
                        className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white hover:bg-navy-800"
                    >
                        <Plus className="h-4 w-4" />
                        Nueva noticia
                    </Link>
                </div>

                <Card className="mt-6 p-4">
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
                </Card>

                <div className="mt-6">
                <TableContainer>
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
                                            <span className="ml-2 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold uppercase text-amber-800">
                                                Destacada
                                            </span>
                                        ) : null}
                                    </td>
                                    <td className="px-4 py-3 text-plomo">
                                        {n.category?.name ?? '—'}
                                    </td>
                                    <td className="px-4 py-3 capitalize">{n.status}</td>
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
                </TableContainer>
                </div>
            </PageContainer>
        </IntranetLayout>
    );
}
