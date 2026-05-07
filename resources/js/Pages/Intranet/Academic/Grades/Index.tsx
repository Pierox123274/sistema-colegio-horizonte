import { Card } from '@/Components/Intranet/Card';
import { EmptyState } from '@/Components/Intranet/EmptyState';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { StatsCard } from '@/Components/Intranet/StatsCard';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import { statsIcon } from '@/Components/Intranet/navIcons';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { BookMarked, Filter, Plus, Trash2 } from 'lucide-react';
import { FormEvent, useState } from 'react';

type GradeRow = {
    id: number;
    code: string;
    name: string;
    order: number;
    is_active: boolean;
    educational_level?: { id: number; code: string; name: string };
    sections_count?: number;
};

type PaginatorLink = { url: string | null; label: string; active: boolean };

type LaravelPaginator = {
    data: GradeRow[];
    links: PaginatorLink[];
};

type IndexPageProps = PageProps<{
    grades: LaravelPaginator;
    stats: {
        grades_total: number;
        grades_active: number;
        levels_total: number;
    };
    filters: {
        search: string;
        educational_level_id: string;
        is_active: string;
    };
    catalog: { educational_levels: SelectOption[] };
    permissions: { manage: boolean };
}>;

export default function GradesIndex() {
    const { grades, stats, filters, catalog, permissions, flash } =
        usePage<IndexPageProps>().props;

    const [search, setSearch] = useState(String(filters.search ?? ''));
    const [levelId, setLevelId] = useState(
        String(filters.educational_level_id ?? ''),
    );
    const [isActive, setIsActive] = useState(String(filters.is_active ?? ''));

    const applyFilters = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('intranet.academic.grades.index'),
            {
                search: search || undefined,
                educational_level_id: levelId || undefined,
                is_active: isActive !== '' ? isActive : undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const rows = grades.data ?? [];

    const destroy = (id: number, label: string) => {
        if (
            !confirm(
                `¿Eliminar el grado «${label}»? Solo si no tiene secciones.`,
            )
        ) {
            return;
        }
        router.delete(route('intranet.academic.grades.destroy', id));
    };

    return (
        <IntranetLayout title="Grados">
            <Head title="Grados — Horizonte" />

            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Grados' }]} />

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
                    title="Grados"
                    description="Grados por nivel educativo (orden y código únicos por nivel)."
                    actions={
                        permissions.manage ? (
                            <Link
                                href={route('intranet.academic.grades.create')}
                                className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                            >
                                <Plus className="h-4 w-4" aria-hidden />
                                Nuevo grado
                            </Link>
                        ) : null
                    }
                />

                <div className="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <StatsCard
                        title="Grados"
                        value={String(stats.grades_total)}
                        subtitle="Registrados"
                        icon={statsIcon('book-marked')}
                        accent="navy"
                    />
                    <StatsCard
                        title="Activos"
                        value={String(stats.grades_active)}
                        subtitle="En operación"
                        icon={BookMarked}
                        accent="yellow"
                    />
                    <StatsCard
                        title="Niveles"
                        value={String(stats.levels_total)}
                        subtitle="Configurados"
                        icon={statsIcon('layers')}
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
                        <div className="w-full min-w-[200px] sm:w-auto">
                            <label
                                htmlFor="educational_level_id"
                                className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                            >
                                Nivel
                            </label>
                            <select
                                id="educational_level_id"
                                value={levelId}
                                onChange={(e) => setLevelId(e.target.value)}
                                className="mt-1 w-full rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            >
                                <option value="">Todos los niveles</option>
                                {catalog.educational_levels.map((o) => (
                                    <option key={o.value} value={o.value}>
                                        {o.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="w-full min-w-[140px] sm:w-auto">
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
                                Alta solo administración
                            </span>
                        ) : (
                            <span className="text-xs text-plomo">Solo lectura</span>
                        )
                    }
                >
                    {rows.length === 0 ? (
                        <div className="p-6">
                            <EmptyState
                                icon={BookMarked}
                                title="Sin grados"
                                description="Defina grados por nivel o ajuste los filtros."
                                action={
                                    permissions.manage ? (
                                        <Link
                                            href={route(
                                                'intranet.academic.grades.create',
                                            )}
                                            className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                                        >
                                            <Plus className="h-4 w-4" />
                                            Nuevo grado
                                        </Link>
                                    ) : null
                                }
                            />
                        </div>
                    ) : (
                        <table className="min-w-full text-left text-sm">
                            <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase tracking-wide text-plomo">
                                <tr>
                                    <th className="px-4 py-3 sm:px-6">Nivel</th>
                                    <th className="px-4 py-3 sm:px-6">Orden</th>
                                    <th className="px-4 py-3 sm:px-6">Código</th>
                                    <th className="px-4 py-3 sm:px-6">Nombre</th>
                                    <th className="hidden px-4 py-3 lg:table-cell sm:px-6">
                                        Secciones
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
                                        <td className="px-4 py-3 text-xs text-navy-900 sm:px-6">
                                            <span className="inline-flex rounded-full bg-navy-900/5 px-2 py-0.5 font-semibold ring-1 ring-navy-900/10">
                                                {row.educational_level?.code ??
                                                    '—'}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-navy-900 sm:px-6">
                                            {row.order}
                                        </td>
                                        <td className="px-4 py-3 font-mono text-xs font-semibold text-navy-900 sm:px-6">
                                            {row.code}
                                        </td>
                                        <td className="px-4 py-3 font-medium text-navy-900 sm:px-6">
                                            {row.name}
                                        </td>
                                        <td className="hidden px-4 py-3 lg:table-cell sm:px-6">
                                            {row.sections_count ?? 0}
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
                                                        'intranet.academic.grades.show',
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
                                                                'intranet.academic.grades.edit',
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

                {grades.links && grades.links.length > 3 ? (
                    <nav
                        className="mt-6 flex flex-wrap justify-center gap-1"
                        aria-label="Paginación"
                    >
                        {grades.links.map((link, i) => {
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
