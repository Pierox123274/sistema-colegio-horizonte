import { AppBadge } from '@/Components/App/AppBadge';
import { AppEmptyState } from '@/Components/App/AppEmptyState';
import { AppFilterBar } from '@/Components/App/AppFilterBar';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { AppTable } from '@/Components/App/AppTable';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { Filter, Plus, Receipt } from 'lucide-react';
import { FormEvent, useState } from 'react';

type Row = {
    id: number;
    payment_code: string;
    amount: string;
    payment_method: string;
    paid_at: string;
    status: string;
    student?: { code: string; first_name: string; last_name: string };
    payment_concept?: { code: string; name: string };
    enrollment?: { enrollment_code: string };
};

type PaginatorLink = { url: string | null; label: string; active: boolean };
type LaravelPaginator = { data: Row[]; links: PaginatorLink[] };

type P = PageProps<{
    payments: LaravelPaginator;
    filters: {
        search: string;
        status: string;
        payment_method: string;
        month: string;
        year: string;
        paid_from: string;
        paid_to: string;
    };
    catalog: { methods: SelectOption[]; statuses: SelectOption[] };
}>;

export default function PaymentsIndex() {
    const { payments, filters, catalog, flash } = usePage<P>().props;
    const [search, setSearch] = useState(String(filters.search ?? ''));
    const [status, setStatus] = useState(String(filters.status ?? ''));
    const [payment_method, setPaymentMethod] = useState(
        String(filters.payment_method ?? ''),
    );
    const [month, setMonth] = useState(String(filters.month ?? ''));
    const [year, setYear] = useState(String(filters.year ?? ''));
    const [paid_from, setPaidFrom] = useState(String(filters.paid_from ?? ''));
    const [paid_to, setPaidTo] = useState(String(filters.paid_to ?? ''));

    const apply = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('intranet.payments.index'),
            {
                search: search || undefined,
                status: status || undefined,
                payment_method: payment_method || undefined,
                month: month || undefined,
                year: year || undefined,
                paid_from: paid_from || undefined,
                paid_to: paid_to || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const rows = payments.data ?? [];

    return (
        <IntranetLayout title="Pagos">
            <Head title="Pagos — Horizonte" />

            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Finanzas' },
                        { label: 'Pagos' },
                    ]}
                />

                {flash?.success ? (
                    <div className="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        {flash.success}
                    </div>
                ) : null}

                <AppPageHeader
                    title="Pagos"
                    description="Registro de cobros y asociación con pensiones."
                    eyebrow="Finanzas"
                    actions={
                        <Link
                            href={route('intranet.payments.create')}
                            className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-navy-950"
                        >
                            <Plus className="h-4 w-4" />
                            Registrar pago
                        </Link>
                    }
                />

                <AppFilterBar className="mb-6">
                    <form
                        onSubmit={apply}
                        className="grid gap-4 lg:grid-cols-[1fr_auto_auto_auto_auto_auto_auto_auto]"
                    >
                        <div>
                            <label className="block text-xs font-semibold uppercase text-plomo">
                                Estudiante / código pago
                            </label>
                            <input
                                type="search"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-semibold uppercase text-plomo">
                                Estado
                            </label>
                            <select
                                value={status}
                                onChange={(e) => setStatus(e.target.value)}
                                className="mt-1 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">Todos</option>
                                {catalog.statuses.map((s) => (
                                    <option key={s.value} value={s.value}>
                                        {s.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-semibold uppercase text-plomo">
                                Método
                            </label>
                            <select
                                value={payment_method}
                                onChange={(e) =>
                                    setPaymentMethod(e.target.value)
                                }
                                className="mt-1 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">Todos</option>
                                {catalog.methods.map((s) => (
                                    <option key={s.value} value={s.value}>
                                        {s.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-semibold uppercase text-plomo">
                                Mes (pago)
                            </label>
                            <select
                                value={month}
                                onChange={(e) => setMonth(e.target.value)}
                                className="mt-1 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">—</option>
                                {Array.from({ length: 12 }, (_, i) => i + 1).map(
                                    (m) => (
                                        <option key={m} value={String(m)}>
                                            {m}
                                        </option>
                                    ),
                                )}
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-semibold uppercase text-plomo">
                                Año
                            </label>
                            <input
                                type="number"
                                min={2000}
                                max={2100}
                                value={year}
                                onChange={(e) => setYear(e.target.value)}
                                className="mt-1 w-24 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-semibold uppercase text-plomo">
                                Desde
                            </label>
                            <input
                                type="date"
                                value={paid_from}
                                onChange={(e) => setPaidFrom(e.target.value)}
                                className="mt-1 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-semibold uppercase text-plomo">
                                Hasta
                            </label>
                            <input
                                type="date"
                                value={paid_to}
                                onChange={(e) => setPaidTo(e.target.value)}
                                className="mt-1 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            />
                        </div>
                        <div className="flex items-end">
                            <button
                                type="submit"
                                className="inline-flex items-center gap-2 rounded-lg border border-plomo/20 bg-white px-4 py-2 text-sm font-semibold shadow-sm"
                            >
                                <Filter className="h-4 w-4" />
                                Filtrar
                            </button>
                        </div>
                    </form>
                </AppFilterBar>

                <AppTable title="Listado" description={`${rows.length} registros en esta página.`}>
                    {rows.length === 0 ? (
                        <div className="p-6">
                            <AppEmptyState
                                icon={Receipt}
                                title="Sin pagos"
                                description="Registre el primer cobro desde el botón superior."
                                action={
                                    <Link
                                        href={route(
                                            'intranet.payments.create',
                                        )}
                                        className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                                    >
                                        <Plus className="h-4 w-4" />
                                        Registrar pago
                                    </Link>
                                }
                            />
                        </div>
                    ) : (
                        <table className="min-w-full text-left text-sm">
                            <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo">
                                <tr>
                                    <th className="px-4 py-3">Código</th>
                                    <th className="px-4 py-3">Estudiante</th>
                                    <th className="hidden px-4 py-3 lg:table-cell">
                                        Concepto
                                    </th>
                                    <th className="px-4 py-3">Monto</th>
                                    <th className="px-4 py-3">Método</th>
                                    <th className="px-4 py-3">Fecha</th>
                                    <th className="px-4 py-3">Estado</th>
                                    <th className="px-4 py-3 text-right">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-plomo/10">
                                {rows.map((r) => (
                                    <tr key={r.id} className="hover:bg-navy-50/40">
                                        <td className="px-4 py-3 font-mono text-xs">
                                            {r.payment_code}
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="font-medium text-navy-900">
                                                {r.student
                                                    ? `${r.student.first_name} ${r.student.last_name}`
                                                    : '—'}
                                            </div>
                                            <div className="text-[11px] text-plomo">
                                                {r.student?.code}
                                            </div>
                                        </td>
                                        <td className="hidden px-4 py-3 text-xs lg:table-cell">
                                            {r.payment_concept?.name ?? '—'}
                                        </td>
                                        <td className="px-4 py-3 font-mono text-xs">
                                            S/{' '}
                                            {Number(r.amount).toFixed(2)}
                                        </td>
                                        <td className="px-4 py-3 text-xs">
                                            {r.payment_method}
                                        </td>
                                        <td className="px-4 py-3 text-xs">
                                            {r.paid_at?.slice(0, 16)?.replace(
                                                'T',
                                                ' ',
                                            )}
                                        </td>
                                        <td className="px-4 py-3">
                                            <AppBadge
                                                tone={
                                                    r.status === 'registrado'
                                                        ? 'success'
                                                        : 'neutral'
                                                }
                                            >
                                                {r.status}
                                            </AppBadge>
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <Link
                                                href={route(
                                                    'intranet.payments.show',
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
                </AppTable>

                {payments.links?.length > 3 ? (
                    <nav className="mt-6 flex flex-wrap justify-center gap-1">
                        {payments.links.map((link, i) =>
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
