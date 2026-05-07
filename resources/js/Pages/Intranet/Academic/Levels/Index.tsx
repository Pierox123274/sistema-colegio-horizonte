import { Card } from '@/Components/Intranet/Card';
import { EmptyState } from '@/Components/Intranet/EmptyState';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { StatsCard } from '@/Components/Intranet/StatsCard';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import { statsIcon } from '@/Components/Intranet/navIcons';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { Filter, Layers, Plus, Trash2 } from 'lucide-react';
import { FormEvent, useState } from 'react';

type LevelRow = {
    id: number;
    code: string;
    name: string;
    description: string | null;
    is_active: boolean;
    grades_count?: number;
};

type PaginatorLink = { url: string | null; label: string; active: boolean };

type LaravelPaginator = {
    data: LevelRow[];
    links: PaginatorLink[];
};

type IndexPageProps = PageProps<{
    levels: LaravelPaginator;
    stats: {
        levels_total: number;
        levels_active: number;
        grades_total: number;
    };
    filters: { search: string; is_active: string };
    permissions: { manage: boolean };
}>;

export default function LevelsIndex() {
    const { levels, stats, filters, permissions, flash } =
        usePage<IndexPageProps>().props;

    const [search, setSearch] = useState(String(filters.search ?? ''));
    const [isActive, setIsActive] = useState(String(filters.is_active ?? ''));

    const applyFilters = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('intranet.academic.levels.index'),
            {
                search: search || undefined,
                is_active: isActive !== '' ? isActive : undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const rows = levels.data ?? [];

    const destroy = (id: number, label: string) => {
        if (
            !confirm(
                `¿Eliminar el nivel «${label}»? Solo es posible si no tiene grados.`,
            )
        ) {
            return;
        }
        router.delete(route('intranet.academic.levels.destroy', id));
    };

    return (
        <IntranetLayout title="Niveles educativos">
            <Head title="Niveles educativos — Horizonte" />

            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Niveles educativos' }]} />

                {flash?.success ? (
                    <div
                        className="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900"
                        role="status"
                    >
                        {flash.success}
                    </div>
                ) : null}
                {flash?.error ? (
                    <div
                        className="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900"
                        role="alert"
                    >
                        {flash.error}
                    </div>
                ) : null}

                <SectionTitle
                    title="Niveles educativos"
                    description="Inicial, primaria y secundaria — estructura institucional."
                    actions={
                        permissions.manage ? (
                            <Link
                                href={route('intranet.academic.levels.create')}
                                className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                            >
                                <Plus className="h-4 w-4" aria-hidden />
                                Nuevo nivel
                            </Link>
                        ) : null
                    }
                />

                <div className="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <StatsCard
                        title="Niveles"
                        value={String(stats.levels_total)}
                        subtitle="Registrados"
                        icon={statsIcon('layers')}
                        accent="navy"
                    />
                    <StatsCard
                        title="Activos"
                        value={String(stats.levels_active)}
                        subtitle="Visibles en operación"
                        icon={Layers}
                        accent="yellow"
                    />
                    <StatsCard
                        title="Grados (total)"
                        value={String(stats.grades_total)}
                        subtitle="En todos los niveles"
                        icon={statsIcon('book-marked')}
                        accent="navy"
                    />
                </div>

                <Card className="mb-6">
                    <form
                        onSubmit={applyFilters}
                        className="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-end"
                    >
                        <div className="min-w-[200px] flex-1">
                            <label
                                htmlFor="search"
                                className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                            >
                                Buscar
                            </label>
                            <input
                                id="search"
                                type="search"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Código o nombre"
                                className="mt-1 w-full rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            />
                        </div>
                        <div className="w-full min-w-[160px] sm:w-auto">
                            <label
                                htmlFor="is_active"
                                className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                            >
                                Estado
                            </label>
                            <select
                                id="is_active"
                                value={isActive}
                                onChange={(e) => setIsActive(e.target.value)}
                                className="mt-1 w-full rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            >
                                <option value="">Todos</option>
                                <option value="1">Activos</option>
                                <option value="0">Inactivos</option>
                            </select>
                        </div>
                        <button
                            type="submit"
                            className="inline-flex items-center justify-center gap-2 rounded-lg border border-plomo/20 bg-white px-4 py-2 text-sm font-semibold text-navy-900 shadow-sm transition hover:bg-navy-50"
                        >
                            <Filter className="h-4 w-4" aria-hidden />
                            Aplicar filtros
                        </button>
                    </form>
                </Card>

                <TableContainer
                    title="Listado"
                    description={`${rows.length} registros en esta página.`}
                    toolbar={
                        permissions.manage ? (
                            <span className="text-xs text-plomo">
                                Alta y baja solo administración
                            </span>
                        ) : (
                            <span className="text-xs text-plomo">Solo lectura</span>
                        )
                    }
                >
                    {rows.length === 0 ? (
                        <div className="p-6">
                            <EmptyState
                                icon={Layers}
                                title="Sin niveles"
                                description="Cree el primer nivel o ajuste los filtros."
                                action={
                                    permissions.manage ? (
                                        <Link
                                            href={route(
                                                'intranet.academic.levels.create',
                                            )}
                                            className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                                        >
                                            <Plus className="h-4 w-4" />
                                            Nuevo nivel
                                        </Link>
                                    ) : null
                                }
                            />
                        </div>
                    ) : (
                        <table className="min-w-full text-left text-sm">
                            <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase tracking-wide text-plomo">
                                <tr>
                                    <th className="px-4 py-3 sm:px-6">Código</th>
                                    <th className="px-4 py-3 sm:px-6">Nombre</th>
                                    <th className="hidden px-4 py-3 lg:table-cell sm:px-6">
                                        Grados
                                    </th>
                                    <th className="px-4 py-3 sm:px-6">Estado</th>
                                    <th className="px-4 py-3 text-right sm:px-6">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-plomo/10">
                                {rows.map((row) => (
                                    <tr
                                        key={row.id}
                                        className="bg-white hover:bg-navy-50/40"
                                    >
                                        <td className="px-4 py-3 font-mono text-xs font-semibold text-navy-900 sm:px-6">
                                            {row.code}
                                        </td>
                                        <td className="px-4 py-3 sm:px-6">
                                            <div className="font-medium text-navy-900">
                                                {row.name}
                                            </div>
                                            {row.description ? (
                                                <p className="mt-0.5 line-clamp-1 text-xs text-plomo">
                                                    {row.description}
                                                </p>
                                            ) : null}
                                        </td>
                                        <td className="hidden px-4 py-3 text-navy-900 lg:table-cell sm:px-6">
                                            {row.grades_count ?? 0}
                                        </td>
                                        <td className="px-4 py-3 sm:px-6">
                                            <span
                                                className={`inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ${
                                                    row.is_active
                                                        ? 'bg-emerald-50 text-emerald-900 ring-emerald-200'
                                                        : 'bg-plomo/10 text-plomo ring-plomo/20'
                                                }`}
                                            >
                                                {row.is_active ? 'Activo' : 'Inactivo'}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-right sm:px-6">
                                            <div className="flex justify-end gap-3">
                                                <Link
                                                    href={route(
                                                        'intranet.academic.levels.show',
                                                        row.id,
                                                    )}
                                                    className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                                                >
                                                    Ver
                                                </Link>
                                                {permissions.manage ? (
                                                    <>
                                                        <Link
                                                            href={route(
                                                                'intranet.academic.levels.edit',
                                                                row.id,
                                                            )}
                                                            className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                                                        >
                                                            Editar
                                                        </Link>
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                destroy(
                                                                    row.id,
                                                                    row.name,
                                                                )
                                                            }
                                                            className="inline-flex items-center gap-1 text-sm font-semibold text-brand-red hover:underline"
                                                        >
                                                            <Trash2 className="h-3.5 w-3.5" />
                                                            Eliminar
                                                        </button>
                                                    </>
                                                ) : null}
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </TableContainer>

                {levels.links && levels.links.length > 3 ? (
                    <nav
                        className="mt-6 flex flex-wrap justify-center gap-1"
                        aria-label="Paginación"
                    >
                        {levels.links.map((link, i) => {
                            if (!link.url) {
                                return (
                                    <span
                                        key={i}
                                        className="rounded-md px-3 py-1.5 text-sm text-plomo"
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                );
                            }
                            return (
                                <button
                                    key={i}
                                    type="button"
                                    className={`rounded-md px-3 py-1.5 text-sm ${
                                        link.active
                                            ? 'bg-navy-900 font-semibold text-white'
                                            : 'border border-plomo/20 bg-white text-navy-900 hover:bg-navy-50'
                                    }`}
                                    onClick={() => router.visit(link.url!)}
                                    dangerouslySetInnerHTML={{
                                        __html: link.label,
                                    }}
                                />
                            );
                        })}
                    </nav>
                ) : null}
            </PageContainer>
        </IntranetLayout>
    );
}
