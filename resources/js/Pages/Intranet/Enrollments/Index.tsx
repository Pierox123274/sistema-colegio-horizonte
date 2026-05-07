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
import { ClipboardList, Filter, Plus } from 'lucide-react';
import { FormEvent, useMemo, useState } from 'react';

const STATUS_BADGE: Record<string, string> = {
    pendiente: 'bg-amber-50 text-amber-900 ring-amber-200',
    matriculado: 'bg-emerald-50 text-emerald-900 ring-emerald-200',
    anulado: 'bg-plomo/15 text-plomo ring-plomo/25',
    retirado: 'bg-rose-50 text-rose-900 ring-rose-200',
};

type Row = {
    id: number;
    enrollment_code: string;
    enrollment_date: string;
    amount: string;
    status: string;
    student?: { code: string; first_name: string; last_name: string };
    academic_year?: { name: string; year: number };
    educational_level?: { code: string };
    grade?: { code: string; name: string };
};

type PaginatorLink = { url: string | null; label: string; active: boolean };
type LaravelPaginator = { data: Row[]; links: PaginatorLink[] };

type GradeOpt = SelectOption & { educational_level_id?: number };

type IndexPageProps = PageProps<{
    enrollments: LaravelPaginator;
    stats: {
        enrollments_total: number;
        enrollments_pending: number;
        enrollments_active_year: number;
    };
    filters: {
        search: string;
        academic_year_id: string;
        educational_level_id: string;
        grade_id: string;
        status: string;
    };
    catalog: {
        academic_years: SelectOption[];
        levels: SelectOption[];
        grades: GradeOpt[];
        statuses: SelectOption[];
    };
    permissions: { manage: boolean };
}>;

export default function EnrollmentsIndex() {
    const { enrollments, stats, filters, catalog, permissions, flash } =
        usePage<IndexPageProps>().props;

    const [search, setSearch] = useState(String(filters.search ?? ''));
    const [academicYearId, setAcademicYearId] = useState(
        String(filters.academic_year_id ?? ''),
    );
    const [levelId, setLevelId] = useState(
        String(filters.educational_level_id ?? ''),
    );
    const [gradeId, setGradeId] = useState(String(filters.grade_id ?? ''));
    const [status, setStatus] = useState(String(filters.status ?? ''));

    const gradeOptions = useMemo(() => {
        if (!levelId) {
            return catalog.grades;
        }
        const lid = Number(levelId);
        return catalog.grades.filter(
            (g) => g.educational_level_id === lid,
        );
    }, [catalog.grades, levelId]);

    const applyFilters = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('intranet.enrollments.index'),
            {
                search: search || undefined,
                academic_year_id: academicYearId || undefined,
                educational_level_id: levelId || undefined,
                grade_id: gradeId || undefined,
                status: status || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const rows = enrollments.data ?? [];

    return (
        <IntranetLayout title="Matrículas">
            <Head title="Matrículas — Horizonte" />

            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Matrículas' }]} />

                {flash?.success ? (
                    <div
                        className="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900"
                        role="status"
                    >
                        {flash.success}
                    </div>
                ) : null}

                <SectionTitle
                    title="Matrículas"
                    description="Registro institucional por año académico y ubicación curricular."
                    actions={
                        <div className="flex flex-wrap gap-2">
                            {permissions.manage ? (
                                <>
                                    <Link
                                        href={route(
                                            'intranet.academic-years.index',
                                        )}
                                        className="inline-flex items-center rounded-lg border border-plomo/20 bg-white px-4 py-2 text-sm font-semibold text-navy-900 shadow-sm transition hover:bg-navy-50"
                                    >
                                        Años académicos
                                    </Link>
                                    <Link
                                        href={route(
                                            'intranet.enrollments.create',
                                        )}
                                        className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                                    >
                                        <Plus className="h-4 w-4" aria-hidden />
                                        Nueva matrícula
                                    </Link>
                                </>
                            ) : null}
                        </div>
                    }
                />

                <div className="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <StatsCard
                        title="Matrículas"
                        value={String(stats.enrollments_total)}
                        subtitle="Total registradas"
                        icon={statsIcon('clipboard-list')}
                        accent="navy"
                    />
                    <StatsCard
                        title="Pendientes"
                        value={String(stats.enrollments_pending)}
                        subtitle="Por confirmar"
                        icon={ClipboardList}
                        accent="yellow"
                    />
                    <StatsCard
                        title="Años activos"
                        value={String(stats.enrollments_active_year)}
                        subtitle="Marcados como vigentes"
                        icon={statsIcon('layers')}
                        accent="navy"
                    />
                </div>

                <Card className="mb-6">
                    <form
                        onSubmit={applyFilters}
                        className="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-end"
                    >
                        <div className="min-w-[180px] flex-1">
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
                                placeholder="Código, nombre o código estudiante"
                                className="mt-1 w-full rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            />
                        </div>
                        <div className="w-full min-w-[160px] sm:w-auto">
                            <label className="block text-xs font-semibold uppercase tracking-wide text-plomo">
                                Año académico
                            </label>
                            <select
                                value={academicYearId}
                                onChange={(e) =>
                                    setAcademicYearId(e.target.value)
                                }
                                className="mt-1 w-full rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            >
                                <option value="">Todos</option>
                                {catalog.academic_years.map((o) => (
                                    <option key={o.value} value={o.value}>
                                        {o.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="w-full min-w-[140px] sm:w-auto">
                            <label className="block text-xs font-semibold uppercase tracking-wide text-plomo">
                                Nivel
                            </label>
                            <select
                                value={levelId}
                                onChange={(e) => {
                                    setLevelId(e.target.value);
                                    setGradeId('');
                                }}
                                className="mt-1 w-full rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            >
                                <option value="">Todos</option>
                                {catalog.levels.map((o) => (
                                    <option key={o.value} value={o.value}>
                                        {o.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="w-full min-w-[160px] sm:w-auto">
                            <label className="block text-xs font-semibold uppercase tracking-wide text-plomo">
                                Grado
                            </label>
                            <select
                                value={gradeId}
                                onChange={(e) => setGradeId(e.target.value)}
                                className="mt-1 w-full rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            >
                                <option value="">Todos</option>
                                {gradeOptions.map((o) => (
                                    <option key={o.value} value={o.value}>
                                        {o.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="w-full min-w-[140px] sm:w-auto">
                            <label className="block text-xs font-semibold uppercase tracking-wide text-plomo">
                                Estado
                            </label>
                            <select
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
                    description={`${rows.length} registros en esta página.`}
                    toolbar={
                        permissions.manage ? (
                            <span className="text-xs text-plomo">
                                Alta y edición: administración y secretaría
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
                                icon={ClipboardList}
                                title="Sin matrículas"
                                description="Registre una matrícula o ajuste los filtros."
                                action={
                                    permissions.manage ? (
                                        <Link
                                            href={route(
                                                'intranet.enrollments.create',
                                            )}
                                            className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                                        >
                                            <Plus className="h-4 w-4" />
                                            Nueva matrícula
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
                                    <th className="px-4 py-3 sm:px-6">
                                        Estudiante
                                    </th>
                                    <th className="hidden px-4 py-3 lg:table-cell sm:px-6">
                                        Año
                                    </th>
                                    <th className="hidden px-4 py-3 md:table-cell sm:px-6">
                                        Ubicación
                                    </th>
                                    <th className="px-4 py-3 sm:px-6">Estado</th>
                                    <th className="hidden px-4 py-3 sm:table-cell sm:px-6">
                                        Monto
                                    </th>
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
                                            {row.enrollment_code}
                                        </td>
                                        <td className="px-4 py-3 sm:px-6">
                                            <div className="font-medium text-navy-900">
                                                {row.student
                                                    ? `${row.student.first_name} ${row.student.last_name}`
                                                    : '—'}
                                            </div>
                                            <div className="text-[11px] text-plomo">
                                                {row.student?.code ?? ''}
                                            </div>
                                        </td>
                                        <td className="hidden px-4 py-3 text-xs lg:table-cell sm:px-6">
                                            {row.academic_year?.name ?? '—'}
                                        </td>
                                        <td className="hidden px-4 py-3 text-xs md:table-cell sm:px-6">
                                            <span className="inline-flex rounded bg-navy-900/5 px-1.5 py-0.5 font-mono text-[11px] font-semibold text-navy-900">
                                                {row.educational_level?.code ??
                                                    ''}{' '}
                                                · {row.grade?.name ?? ''}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 sm:px-6">
                                            <span
                                                className={`inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ${
                                                    STATUS_BADGE[row.status] ??
                                                    'bg-plomo/10 text-plomo ring-plomo/20'
                                                }`}
                                            >
                                                {catalog.statuses.find(
                                                    (s) =>
                                                        s.value === row.status,
                                                )?.label ?? row.status}
                                            </span>
                                        </td>
                                        <td className="hidden px-4 py-3 font-mono text-xs sm:table-cell sm:px-6">
                                            S/ {Number(row.amount).toFixed(2)}
                                        </td>
                                        <td className="px-4 py-3 text-right sm:px-6">
                                            <Link
                                                href={route(
                                                    'intranet.enrollments.show',
                                                    row.id,
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

                {enrollments.links && enrollments.links.length > 3 ? (
                    <nav
                        className="mt-6 flex flex-wrap justify-center gap-1"
                        aria-label="Paginación"
                    >
                        {enrollments.links.map((link, i) => {
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
