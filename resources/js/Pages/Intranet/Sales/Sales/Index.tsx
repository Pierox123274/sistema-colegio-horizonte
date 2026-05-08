import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { FormEvent, useState } from 'react';

type P = PageProps<{
    sales: { data: Array<{ id: number; sale_code: string; status: string; total: string; sold_at: string; student?: { first_name: string; last_name: string; code: string } }> };
    filters: { search: string; status: string; date_from: string; date_to: string; day: string; payment_method: string; cashier_id: string };
    catalog: { statuses: Array<{ value: string; label: string }>; methods: Array<{ value: string; label: string }> };
}>;

export default function SalesIndex() {
    const { sales, filters, catalog } = usePage<P>().props;
    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status ?? '');
    const [dateFrom, setDateFrom] = useState(filters.date_from ?? '');
    const [dateTo, setDateTo] = useState(filters.date_to ?? '');
    const [day, setDay] = useState(filters.day ?? '');
    const [paymentMethod, setPaymentMethod] = useState(filters.payment_method ?? '');
    const [cashierId, setCashierId] = useState(filters.cashier_id ?? '');

    const apply = (e: FormEvent) => {
        e.preventDefault();
        router.get(route('intranet.sales.sales.index'), {
            search: search || undefined,
            status: status || undefined,
            date_from: dateFrom || undefined,
            date_to: dateTo || undefined,
            day: day || undefined,
            payment_method: paymentMethod || undefined,
            cashier_id: cashierId || undefined,
        }, { preserveState: true, replace: true });
    };

    return (
        <IntranetLayout title="Ventas">
            <Head title="Ventas" />
            <PageContainer>
                <SectionTitle
                    title="Ventas"
                    description="Listado y trazabilidad de ventas."
                    actions={<div className="flex gap-3">
                        <Link href={route('intranet.sales.sales.create')} className="text-sm font-semibold text-navy-900 underline">Nueva venta</Link>
                        <Link href={route('intranet.sales.reports.export.pdf', { search: search || undefined, status: status || undefined, date_from: dateFrom || undefined, date_to: dateTo || undefined, day: day || undefined, payment_method: paymentMethod || undefined, cashier_id: cashierId || undefined })} className="text-sm font-semibold text-navy-900 underline">Descargar PDF</Link>
                        <Link href={route('intranet.sales.reports.export.excel', { search: search || undefined, status: status || undefined, date_from: dateFrom || undefined, date_to: dateTo || undefined, day: day || undefined, payment_method: paymentMethod || undefined, cashier_id: cashierId || undefined })} className="text-sm font-semibold text-navy-900 underline">Descargar Excel</Link>
                    </div>}
                />
                <form onSubmit={apply} className="mb-4 grid gap-3 md:grid-cols-3">
                    <input value={search} onChange={(e) => setSearch(e.target.value)} className="rounded-md border-plomo/25" placeholder="Código o persona" />
                    <select value={status} onChange={(e) => setStatus(e.target.value)} className="rounded-md border-plomo/25">
                        <option value="">Todos</option>
                        {catalog.statuses.map((s) => <option key={s.value} value={s.value}>{s.label}</option>)}
                    </select>
                    <select value={paymentMethod} onChange={(e) => setPaymentMethod(e.target.value)} className="rounded-md border-plomo/25">
                        <option value="">Todos los métodos</option>
                        {catalog.methods.map((m) => <option key={m.value} value={m.value}>{m.label}</option>)}
                    </select>
                    <input type="date" value={day} onChange={(e) => setDay(e.target.value)} className="rounded-md border-plomo/25" placeholder="Día específico" />
                    <input type="date" value={dateFrom} onChange={(e) => setDateFrom(e.target.value)} className="rounded-md border-plomo/25" />
                    <input type="date" value={dateTo} onChange={(e) => setDateTo(e.target.value)} className="rounded-md border-plomo/25" />
                    <input value={cashierId} onChange={(e) => setCashierId(e.target.value)} className="rounded-md border-plomo/25" placeholder="ID cajero (opcional)" />
                    <button type="submit" className="rounded-md border px-3">Filtrar</button>
                </form>
                <TableContainer title="Ventas registradas" description={`${sales.data.length} en esta página`}>
                    <table className="min-w-full text-left text-sm">
                        <thead><tr><th className="px-4 py-2">Código</th><th className="px-4 py-2">Alumno</th><th className="px-4 py-2">Fecha</th><th className="px-4 py-2">Estado</th><th className="px-4 py-2">Total</th><th className="px-4 py-2">Acción</th></tr></thead>
                        <tbody>
                            {sales.data.map((row) => (
                                <tr key={row.id} className="border-t border-plomo/10">
                                    <td className="px-4 py-2 font-mono">{row.sale_code}</td>
                                    <td className="px-4 py-2">{row.student ? `${row.student.first_name} ${row.student.last_name} (${row.student.code})` : '—'}</td>
                                    <td className="px-4 py-2">{row.sold_at?.slice(0, 16)?.replace('T', ' ')}</td>
                                    <td className="px-4 py-2">{row.status}</td>
                                    <td className="px-4 py-2">S/ {Number(row.total).toFixed(2)}</td>
                                    <td className="px-4 py-2"><Link href={route('intranet.sales.sales.show', row.id)} className="text-sm font-semibold text-navy-900 underline">Ver</Link></td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </TableContainer>
            </PageContainer>
        </IntranetLayout>
    );
}

