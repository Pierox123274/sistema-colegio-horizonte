import { Card } from '@/Components/Intranet/Card';
import { EmptyState } from '@/Components/Intranet/EmptyState';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import {
    EDUCATIONAL_LEVEL_LABELS,
    STATUS_LABELS,
    levelBadgeClass,
    statusBadgeClass,
} from '@/lib/studentLabels';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type {
    PageProps,
    SelectOption,
    StudentListRow,
    StudentPrimaryGuardianBrief,
} from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { ExternalLink, Filter, Users } from 'lucide-react';
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
    links: { intranet_index: string };
    has_teaching_assignments?: boolean;
    teacher_portal_scoped?: boolean;
}>;

export default function TeacherStudentsIndex() {
    const {
        students,
        filters,
        catalog,
        permissions,
        links,
        has_teaching_assignments,
        teacher_portal_scoped,
    } = usePage<IndexPageProps>().props;

    const [search, setSearch] = useState(String(filters.search ?? ''));
    const [educationalLevel, setEducationalLevel] = useState(
        String(filters.educational_level ?? ''),
    );
    const [status, setStatus] = useState(String(filters.status ?? ''));

    const applyFilters = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('teacher.students.index'),
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
        <TeacherLayout title="Estudiantes">
            <Head title="Portal docente — Estudiantes" />

            <PageContainer>
                {teacher_portal_scoped && has_teaching_assignments === false ? (
                    <div className="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        No tiene secciones docentes asignadas para el año académico activo.
                        Cuando administración registre una asignación, aquí verá solo a los
                        estudiantes matriculados en esas secciones.
                    </div>
                ) : null}

                <SectionTitle
                    title="Estudiantes"
                    description={
                        teacher_portal_scoped
                            ? 'Alumnado matriculado en sus secciones (año activo). La ficha detallada se abre en el módulo institucional.'
                            : 'Mismo listado filtrable que en el ERP, en vista simplificada para el aula. La ficha detallada se abre en el módulo institucional.'
                    }
                    actions={
                        <Link
                            href={links.intranet_index}
                            className="inline-flex items-center gap-2 rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900 transition hover:bg-navy-50"
                        >
                            <ExternalLink className="h-4 w-4" />
                            Vista ERP
                        </Link>
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
                </Card>

                <TableContainer
                    title="Listado"
                    description={`${students.data?.length ?? 0} registros en esta página. ${
                        permissions.manage
                            ? 'Tiene permisos de gestión en el ERP.'
                            : 'Solo lectura (docente).'
                    }`}
                >
                    {rows.length === 0 ? (
                        <div className="p-6">
                            <EmptyState
                                icon={Users}
                                title="Sin estudiantes"
                                description="No hay registros con estos filtros. Ajuste la búsqueda o consulte con secretaría."
                            />
                        </div>
                    ) : (
                        <table className="min-w-full text-left text-sm">
                            <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase tracking-wide text-plomo">
                                <tr>
                                    <th className="px-4 py-3 sm:px-6">Código</th>
                                    <th className="px-4 py-3 sm:px-6">
                                        Estudiante
                                    </th>
                                    <th className="hidden px-4 py-3 md:table-cell sm:px-6">
                                        Documento
                                    </th>
                                    <th className="px-4 py-3 sm:px-6">Nivel</th>
                                    <th className="hidden px-4 py-3 lg:table-cell sm:px-6">
                                        Grado
                                    </th>
                                    <th className="px-4 py-3 sm:px-6">Estado</th>
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
                                                <span
                                                    className={`inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset ${levelBadgeClass(s.educational_level)}`}
                                                >
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
                                                <span
                                                    className={`inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset ${statusBadgeClass(s.status)}`}
                                                >
                                                    {STATUS_LABELS[s.status] ??
                                                        s.status}
                                                </span>
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
                </TableContainer>

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
        </TeacherLayout>
    );
}
