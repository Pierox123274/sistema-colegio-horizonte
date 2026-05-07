import { Card } from '@/Components/Intranet/Card';
import { EmptyState } from '@/Components/Intranet/EmptyState';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { CalendarDays, Filter, Plus } from 'lucide-react';
import { FormEvent, useState } from 'react';

type Row = {
    id: number;
    month: number;
    year: number;
    amount: string;
    due_date: string;
    status: string;
    enrollment?: { enrollment_code: string; student?: { code: string; first_name: string; last_name: string } };
    payment_concept?: { code: string; name: string };
};

type PaginatorLink = { url: string | null; label: string; active: boolean };
type LaravelPaginator = { data: Row[]; links: PaginatorLink[] };

/** Estilo único para inputs/selects del panel de filtros (altura y radio alineados). */
const FILTER_CONTROL =
    'box-border h-11 w-full rounded-xl border border-plomo/20 bg-white px-4 text-sm text-navy-900 shadow-sm outline-none transition placeholder:text-plomo/50 focus:border-navy-900 focus:ring-1 focus:ring-navy-900';

const FILTER_LABEL = 'block text-xs font-semibold uppercase tracking-wide text-plomo';

const STATUS_BADGE: Record<string, string> = {
    pendiente: 'bg-amber-50 text-amber-900 ring-amber-200',
    parcial: 'bg-sky-50 text-sky-900 ring-sky-200',
    pagado: 'bg-emerald-50 text-emerald-900 ring-emerald-200',
    vencido: 'bg-rose-50 text-rose-900 ring-rose-200',
    anulado: 'bg-plomo/15 text-plomo ring-plomo/25',
};

type P = PageProps<{
    pensions: LaravelPaginator;
    filters: { search: string; status: string; month: string; year: string };
    catalog: { statuses: SelectOption[] };
}>;

export default function PensionsIndex() {
    const { pensions, filters, catalog, flash } = usePage<P>().props;
    const [search, setSearch] = useState(String(filters.search ?? ''));
    const [status, setStatus] = useState(String(filters.status ?? ''));
    const [month, setMonth] = useState(String(filters.month ?? ''));
    const [year, setYear] = useState(String(filters.year ?? ''));

    const apply = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('intranet.pensions.index'),
            {
                search: search || undefined,
                status: status || undefined,
                month: month || undefined,
                year: year || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const rows = pensions.data ?? [];

    return (
        <IntranetLayout title="Pensiones">
            <Head title="Pensiones — Horizonte" />

            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Finanzas' },
                        { label: 'Pensiones' },
                    ]}
                />

                {flash?.success ? (
                    <div className="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        {flash.success}
                    </div>
                ) : null}

                <SectionTitle
                    title="Pensiones"
                    description="Cronograma por matrícula y periodo."
                    actions={
                        <Link
                            href={route('intranet.pensions.create')}
                            className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-navy-950"
                        >
                            <Plus className="h-4 w-4" />
                            Nueva pensión
                        </Link>
                    }
                />

                <Card className="mb-6">
                    <form
                        onSubmit={apply}
                        className="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-end lg:gap-x-3 lg:gap-y-4"
                    >
                        <div className="flex min-w-0 flex-1 flex-col gap-1.5">
                            <label htmlFor="pension-filter-search" className={FILTER_LABEL}>
                                Estudiante / código
                            </label>
                            <input
                                id="pension-filter-search"
                                type="search"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className={FILTER_CONTROL}
                            />
                        </div>
                        <div className="flex w-full flex-col gap-1.5 sm:w-auto sm:min-w-[10.5rem]">
                            <label htmlFor="pension-filter-status" className={FILTER_LABEL}>
                                Estado
                            </label>
                            <select
                                id="pension-filter-status"
                                value={status}
                                onChange={(e) => setStatus(e.target.value)}
                                className={`${FILTER_CONTROL} cursor-pointer`}
                            >
                                <option value="">Todos</option>
                                {catalog.statuses.map((s) => (
                                    <option key={s.value} value={s.value}>
                                        {s.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="flex w-full flex-col gap-1.5 sm:w-auto sm:min-w-[7.5rem]">
                            <label htmlFor="pension-filter-month" className={FILTER_LABEL}>
                                Mes
                            </label>
                            <select
                                id="pension-filter-month"
                                value={month}
                                onChange={(e) => setMonth(e.target.value)}
                                className={`${FILTER_CONTROL} cursor-pointer`}
                            >
                                <option value="">Todos</option>
                                {Array.from({ length: 12 }, (_, i) => i + 1).map(
                                    (m) => (
                                        <option key={m} value={String(m)}>
                                            {m}
                                        </option>
                                    ),
                                )}
                            </select>
                        </div>
                        <div className="flex w-full flex-col gap-1.5 sm:w-auto sm:min-w-[7rem]">
                            <label htmlFor="pension-filter-year" className={FILTER_LABEL}>
                                Año
                            </label>
                            <input
                                id="pension-filter-year"
                                type="number"
                                min={2000}
                                max={2100}
                                value={year}
                                onChange={(e) => setYear(e.target.value)}
                                className={`${FILTER_CONTROL} w-full sm:w-28`}
                            />
                        </div>
                        <div className="flex w-full flex-col gap-1.5 lg:w-auto lg:shrink-0">
                            <span className={`${FILTER_LABEL} hidden lg:block`} aria-hidden>
                                &nbsp;
                            </span>
                            <button
                                type="submit"
                                className="inline-flex h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl border border-plomo/20 bg-white px-4 text-sm font-semibold text-navy-900 shadow-sm transition hover:bg-navy-50/80 lg:w-auto"
                            >
                                <Filter className="h-4 w-4 shrink-0" />
                                Filtrar
                            </button>
                        </div>
                    </form>
                </Card>

                <TableContainer title="Listado" description={`${rows.length} registros.`}>
                    {rows.length === 0 ? (
                        <div className="p-6">
                            <EmptyState
                                icon={CalendarDays}
                                title="Sin pensiones"
                                description="Genere obligaciones desde una matrícula."
                                action={
                                    <Link
                                        href={route('intranet.pensions.create')}
                                        className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                                    >
                                        <Plus className="h-4 w-4" />
                                        Nueva pensión
                                    </Link>
                                }
                            />
                        </div>
                    ) : (
                        <table className="min-w-full text-left text-sm">
                            <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo">
                                <tr>
                                    <th className="px-4 py-3">Periodo</th>
                                    <th className="px-4 py-3">Estudiante</th>
                                    <th className="hidden px-4 py-3 lg:table-cell">
                                        Concepto
                                    </th>
                                    <th className="px-4 py-3">Monto</th>
                                    <th className="px-4 py-3">Estado</th>
                                    <th className="px-4 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-plomo/10">
                                {rows.map((r) => (
                                    <tr key={r.id} className="hover:bg-navy-50/40">
                                        <td className="px-4 py-3 font-mono text-xs">
                                            {r.year}-{String(r.month).padStart(2, '0')}
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="font-medium text-navy-900">
                                                {r.enrollment?.student
                                                    ? `${r.enrollment.student.first_name} ${r.enrollment.student.last_name}`
                                                    : '—'}
                                            </div>
                                            <div className="text-[11px] text-plomo">
                                                {r.enrollment?.enrollment_code}
                                            </div>
                                        </td>
                                        <td className="hidden px-4 py-3 text-xs lg:table-cell">
                                            {r.payment_concept?.name ?? '—'}
                                        </td>
                                        <td className="px-4 py-3 font-mono text-xs">
                                            S/{' '}
                                            {Number(r.amount).toFixed(2)}
                                        </td>
                                        <td className="px-4 py-3">
                                            <span
                                                className={`inline-flex rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ${
                                                    STATUS_BADGE[r.status] ??
                                                    'bg-plomo/10'
                                                }`}
                                            >
                                                {r.status}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <Link
                                                href={route(
                                                    'intranet.pensions.show',
                                                    r.id,
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

                {pensions.links?.length > 3 ? (
                    <nav className="mt-6 flex flex-wrap justify-center gap-1">
                        {pensions.links.map((link, i) =>
                            link.url ? (
                                <button
                                    key={i}
                                    type="button"
                                    className={`rounded-md px-3 py-1.5 text-sm ${
                                        link.active
                                            ? 'bg-navy-900 text-white'
                                            : 'border bg-white'
                                    }`}
                                    onClick={() => router.visit(link.url!)}
                                    dangerouslySetInnerHTML={{
                                        __html: link.label,
                                    }}
                                />
                            ) : (
                                <span
                                    key={i}
                                    className="px-3 py-1.5 text-sm text-plomo"
                                    dangerouslySetInnerHTML={{
                                        __html: link.label,
                                    }}
                                />
                            ),
                        )}
                    </nav>
                ) : null}
            </PageContainer>
        </IntranetLayout>
    );
}
