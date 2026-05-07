import { Card } from '@/Components/Intranet/Card';
import { EmptyState } from '@/Components/Intranet/EmptyState';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import { RELATIONSHIP_LABELS } from '@/lib/guardianLabels';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { GuardianSerializable, PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { Filter, Plus, UserCircle } from 'lucide-react';
import { FormEvent, useState } from 'react';

type PaginatorLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type LaravelPaginator = {
    data: GuardianSerializable[];
    links: PaginatorLink[];
};

type IndexPageProps = PageProps<{
    guardians: LaravelPaginator;
    filters: {
        search: string;
        relationship_type: string;
    };
    catalog: {
        relationship_types: SelectOption[];
    };
    permissions: {
        manage: boolean;
    };
}>;

export default function GuardiansIndex() {
    const { guardians, filters, catalog, permissions, flash } =
        usePage<IndexPageProps>().props;

    const [search, setSearch] = useState(String(filters.search ?? ''));
    const [relationshipType, setRelationshipType] = useState(
        String(filters.relationship_type ?? ''),
    );

    const applyFilters = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('intranet.guardians.index'),
            {
                search: search || undefined,
                relationship_type: relationshipType || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const rows = guardians.data ?? [];

    return (
        <IntranetLayout title="Apoderados">
            <Head title="Apoderados — Horizonte" />

            <PageContainer>
                {flash?.success ? (
                    <div
                        className="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900"
                        role="status"
                    >
                        {flash.success}
                    </div>
                ) : null}

                <SectionTitle
                    title="Apoderados"
                    description="Fichas de familias y vínculos con estudiantes."
                    actions={
                        permissions.manage ? (
                            <Link
                                href={route('intranet.guardians.create')}
                                className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                            >
                                <Plus className="h-4 w-4" aria-hidden />
                                Nuevo apoderado
                            </Link>
                        ) : null
                    }
                />

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
                                placeholder="Nombre, documento o teléfono"
                                className="mt-1 w-full rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            />
                        </div>
                        <div className="w-full min-w-[180px] sm:w-auto">
                            <label
                                htmlFor="relationship_type"
                                className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                            >
                                Parentesco principal
                            </label>
                            <select
                                id="relationship_type"
                                value={relationshipType}
                                onChange={(e) =>
                                    setRelationshipType(e.target.value)
                                }
                                className="mt-1 w-full rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            >
                                <option value="">Todos</option>
                                {catalog.relationship_types.map((o) => (
                                    <option key={o.value} value={o.value}>
                                        {o.label}
                                    </option>
                                ))}
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
                                Alta y edición para administración y secretaría
                            </span>
                        ) : (
                            <span className="text-xs text-plomo">
                                Solo lectura
                            </span>
                        )
                    }
                >
                    {rows.length === 0 ? (
                        <div className="p-6">
                            <EmptyState
                                icon={UserCircle}
                                title="Sin apoderados"
                                description="Registre el primer apoderado o ajuste los filtros."
                                action={
                                    permissions.manage ? (
                                        <Link
                                            href={route(
                                                'intranet.guardians.create',
                                            )}
                                            className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                                        >
                                            <Plus className="h-4 w-4" />
                                            Nuevo apoderado
                                        </Link>
                                    ) : null
                                }
                            />
                        </div>
                    ) : (
                        <table className="min-w-full text-left text-sm">
                            <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase tracking-wide text-plomo">
                                <tr>
                                    <th className="px-4 py-3 sm:px-6">
                                        Apoderado
                                    </th>
                                    <th className="hidden px-4 py-3 md:table-cell sm:px-6">
                                        Documento
                                    </th>
                                    <th className="px-4 py-3 sm:px-6">Teléfono</th>
                                    <th className="hidden px-4 py-3 lg:table-cell sm:px-6">
                                        Parentesco
                                    </th>
                                    <th className="px-4 py-3 sm:px-6">
                                        Estudiantes
                                    </th>
                                    <th className="px-4 py-3 text-right sm:px-6">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-plomo/10">
                                {rows.map((g) => (
                                    <tr
                                        key={g.id}
                                        className="bg-white hover:bg-navy-50/40"
                                    >
                                        <td className="px-4 py-3 sm:px-6">
                                            <div className="font-medium text-navy-900">
                                                {g.first_name} {g.last_name}
                                            </div>
                                            {g.is_emergency_contact ? (
                                                <span className="mt-1 inline-flex rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-900 ring-1 ring-amber-200">
                                                    Emergencia
                                                </span>
                                            ) : null}
                                        </td>
                                        <td className="hidden px-4 py-3 font-mono text-xs text-plomo md:table-cell sm:px-6">
                                            {g.document_number ?? '—'}
                                        </td>
                                        <td className="px-4 py-3 text-navy-900 sm:px-6">
                                            {g.phone}
                                        </td>
                                        <td className="hidden px-4 py-3 lg:table-cell sm:px-6">
                                            <span className="inline-flex rounded-full bg-navy-900/5 px-2.5 py-0.5 text-xs font-semibold text-navy-900 ring-1 ring-navy-900/10">
                                                {RELATIONSHIP_LABELS[
                                                    g.relationship_type
                                                ] ?? g.relationship_type}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-navy-900 sm:px-6">
                                            {g.students_count ?? 0}
                                        </td>
                                        <td className="px-4 py-3 text-right sm:px-6">
                                            <Link
                                                href={route(
                                                    'intranet.guardians.show',
                                                    g.id,
                                                )}
                                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                                            >
                                                Ver
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </TableContainer>

                {guardians.links && guardians.links.length > 3 ? (
                    <nav
                        className="mt-6 flex flex-wrap justify-center gap-1"
                        aria-label="Paginación"
                    >
                        {guardians.links.map((link, i) => {
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
