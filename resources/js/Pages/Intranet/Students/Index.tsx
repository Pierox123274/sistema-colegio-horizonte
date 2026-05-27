import { AppBadge } from '@/Components/App/AppBadge';
import { AppEmptyState } from '@/Components/App/AppEmptyState';
import { AppFilterBar } from '@/Components/App/AppFilterBar';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { AppTable } from '@/Components/App/AppTable';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import {
    EDUCATIONAL_LEVEL_LABELS,
    STATUS_LABELS,
    levelBadgeClass,
    statusBadgeClass,
} from '@/lib/studentLabels';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type {
    PageProps,
    SelectOption,
    StudentListRow,
    StudentPrimaryGuardianBrief,
} from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { Filter, Plus, Users } from 'lucide-react';
import { FormEvent, useState } from 'react';

type PaginatorLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type LaravelPaginator = {
    data: StudentListRow[];
    links: PaginatorLink[];
};

type IndexPageProps = PageProps<{
    students: LaravelPaginator;
    filters: {
        search: string;
        educational_level: string;
        status: string;
    };
    catalog: {
        educational_levels: SelectOption[];
        statuses: SelectOption[];
    };
    permissions: {
        manage: boolean;
    };
    teacher_section_scope?: boolean;
    has_teaching_assignments?: boolean;
}>;

export default function StudentsIndex() {
    const {
        students,
        filters,
        catalog,
        permissions,
        flash,
        teacher_section_scope,
        has_teaching_assignments,
    } = usePage<IndexPageProps>().props;

    const [search, setSearch] = useState(String(filters.search ?? ''));
    const [educationalLevel, setEducationalLevel] = useState(
        String(filters.educational_level ?? ''),
    );
    const [status, setStatus] = useState(String(filters.status ?? ''));

    const applyFilters = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('intranet.students.index'),
            {
                search: search || undefined,
                educational_level: educationalLevel || undefined,
                status: status || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const rows = students.data ?? [];

    const primaryGuardian = (
        row: StudentListRow,
    ): StudentPrimaryGuardianBrief | undefined => row.guardians?.[0];

    return (
        <IntranetLayout title="Estudiantes">
            <Head title="Estudiantes — Horizonte" />

            <PageContainer>
                {flash?.success ? (
                    <div
                        className="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900"
                        role="status"
                    >
                        {flash.success}
                    </div>
                ) : null}

                {teacher_section_scope && has_teaching_assignments === false ? (
                    <div className="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        Como docente, solo vería estudiantes de sus secciones asignadas
                        en el año activo. Aún no tiene asignaciones: el listado aparecerá
                        vacío hasta que administración registre una asignación docente.
                    </div>
                ) : null}

                <AppPageHeader
                    title="Estudiantes"
                    description={
                        teacher_section_scope
                            ? 'Estudiantes matriculados en sus secciones (año académico activo).'
                            : 'Registro institucional del alumnado. Use filtros para localizar fichas.'
                    }
                    actions={
                        permissions.manage ? (
                            <Link
                                href={route('intranet.students.create')}
                                className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-800"
                            >
                                <Plus className="h-4 w-4" aria-hidden />
                                Nuevo estudiante
                            </Link>
                        ) : null
                    }
                />

                <AppFilterBar className="mb-6">
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
                                placeholder="Nombre, código o documento"
                                className="mt-1 w-full rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            />
                        </div>
                        <div className="w-full min-w-[160px] sm:w-auto">
                            <label
                                htmlFor="educational_level"
                                className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                            >
                                Nivel
                            </label>
                            <select
                                id="educational_level"
                                value={educationalLevel}
                                onChange={(e) =>
                                    setEducationalLevel(e.target.value)
                                }
                                className="mt-1 w-full rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            >
                                <option value="">Todos</option>
                                {catalog.educational_levels.map((o) => (
                                    <option key={o.value} value={o.value}>
                                        {o.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="w-full min-w-[160px] sm:w-auto">
                            <label
                                htmlFor="status"
                                className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                            >
                                Estado
                            </label>
                            <select
                                id="status"
                                value={status}
                                onChange={(e) => setStatus(e.target.value)}
                                className="mt-1 w-full rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            >
                                <option value="">Todos</option>
                                {catalog.statuses.map((o) => (
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
                </AppFilterBar>

                <AppTable
                    title="Listado"
                    description={`${students.data?.length ?? 0} registros en esta página.`}
                    toolbar={
                        permissions.manage ? (
                            <span className="text-xs text-plomo">
                                Gestión: alta y edición de fichas
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
                            <AppEmptyState
                                icon={Users}
                                title="Sin estudiantes"
                                description="Aún no hay registros que coincidan con los filtros. Cree el primer estudiante o ajuste la búsqueda."
                                action={
                                    permissions.manage ? (
                                        <Link
                                            href={route(
                                                'intranet.students.create',
                                            )}
                                            className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                                        >
                                            <Plus className="h-4 w-4" />
                                            Nuevo estudiante
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
                                        Código
                                    </th>
                                    <th className="px-4 py-3 sm:px-6">
                                        Estudiante
                                    </th>
                                    <th className="hidden px-4 py-3 md:table-cell sm:px-6">
                                        Documento
                                    </th>
                                    <th className="px-4 py-3 sm:px-6">
                                        Nivel
                                    </th>
                                    <th className="hidden px-4 py-3 lg:table-cell sm:px-6">
                                        Grado
                                    </th>
                                    <th className="px-4 py-3 sm:px-6">
                                        Estado
                                    </th>
                                    <th className="hidden px-4 py-3 md:table-cell sm:px-6">
                                        Apoderado principal
                                    </th>
                                    <th className="px-4 py-3 text-right sm:px-6">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-plomo/10">
                                {rows.map((s) => {
                                    const pg = primaryGuardian(s);
                                    return (
                                    <tr
                                        key={s.id}
                                        className="bg-white hover:bg-navy-50/40"
                                    >
                                        <td className="px-4 py-3 font-mono text-xs font-medium text-navy-900 sm:px-6">
                                            {s.code}
                                        </td>
                                        <td className="px-4 py-3 sm:px-6">
                                            <div className="font-medium text-navy-900">
                                                {s.first_name} {s.last_name}
                                            </div>
                                        </td>
                                        <td className="hidden px-4 py-3 text-plomo md:table-cell sm:px-6">
                                            {s.document_number ?? '—'}
                                        </td>
                                        <td className="px-4 py-3 sm:px-6">
                                            <span className={levelBadgeClass(s.educational_level)}>
                                                {EDUCATIONAL_LEVEL_LABELS[
                                                    s.educational_level
                                                ] ?? s.educational_level}
                                            </span>
                                        </td>
                                        <td className="hidden px-4 py-3 text-navy-900 lg:table-cell sm:px-6">
                                            {s.grade}
                                            {s.section
                                                ? ` · ${s.section}`
                                                : ''}
                                        </td>
                                        <td className="px-4 py-3 sm:px-6">
                                            <AppBadge tone="info">
                                                {STATUS_LABELS[s.status] ??
                                                    s.status}
                                            </AppBadge>
                                        </td>
                                        <td className="hidden px-4 py-3 text-sm md:table-cell sm:px-6">
                                            {pg ? (
                                                <div>
                                                    <div className="font-medium text-navy-900">
                                                        {pg.first_name}{' '}
                                                        {pg.last_name}
                                                    </div>
                                                    <div className="font-mono text-xs text-plomo">
                                                        {pg.phone}
                                                    </div>
                                                </div>
                                            ) : (
                                                <span className="text-plomo">
                                                    —
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-4 py-3 text-right sm:px-6">
                                            <Link
                                                href={route(
                                                    'intranet.students.show',
                                                    s.id,
                                                )}
                                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                                            >
                                                Ver
                                            </Link>
                                        </td>
                                    </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    )}
                </AppTable>

                {students.links && students.links.length > 3 ? (
                    <nav
                        className="mt-6 flex flex-wrap justify-center gap-1"
                        aria-label="Paginación"
                    >
                        {students.links.map((link, i) => {
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
