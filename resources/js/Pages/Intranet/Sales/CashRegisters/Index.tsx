import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/react';

type P = PageProps<{
    cash_registers: { data: Array<{ id: number; business_date: string; status: string; opening_balance: string; closing_balance: string | null; user?: { name: string } }> };
    stats: { open: number; closed: number; total_sales: string; net_cash: string };
    current_open: { id: number } | null;
}>;

export default function CashRegistersIndex() {
    const { cash_registers, stats, current_open } = usePage<P>().props;
    const openForm = useForm({ opening_balance: '0.00', opening_notes: '' });
    const closeForm = useForm({ closing_notes: '' });

    return (
        <IntranetLayout title="Caja diaria">
            <Head title="Caja diaria" />
            <PageContainer>
                <SectionTitle title="Caja diaria" description="Apertura, cierre y resumen de caja." />
                <div className="mb-6 grid gap-4 md:grid-cols-4">
                    <Card><p className="text-xs text-plomo">Cajas abiertas</p><p className="text-2xl font-bold">{stats.open}</p></Card>
                    <Card><p className="text-xs text-plomo">Cajas cerradas</p><p className="text-2xl font-bold">{stats.closed}</p></Card>
                    <Card><p className="text-xs text-plomo">Ventas</p><p className="text-2xl font-bold">S/ {Number(stats.total_sales).toFixed(2)}</p></Card>
                    <Card><p className="text-xs text-plomo">Neto caja</p><p className="text-2xl font-bold">S/ {Number(stats.net_cash).toFixed(2)}</p></Card>
                </div>
                <Card className="mb-6">
                    {current_open ? (
                        <form onSubmit={(e) => {
                            e.preventDefault();
                            closeForm.post(route('intranet.sales.cash-registers.close', current_open.id));
                        }} className="space-y-3">
                            <InputLabel htmlFor="closing_notes" value="Cerrar caja actual" />
                            <textarea id="closing_notes" className="mt-1 block w-full rounded-md border-plomo/25" value={closeForm.data.closing_notes} onChange={(e) => closeForm.setData('closing_notes', e.target.value)} />
                            <PrimaryButton disabled={closeForm.processing}>Cerrar caja</PrimaryButton>
                        </form>
                    ) : (
                        <form onSubmit={(e) => {
                            e.preventDefault();
                            openForm.post(route('intranet.sales.cash-registers.open'));
                        }} className="grid gap-3 sm:grid-cols-2">
                            <div>
                                <InputLabel htmlFor="opening_balance" value="Saldo inicial" />
                                <input id="opening_balance" type="number" min="0" step="0.01" className="mt-1 block w-full rounded-md border-plomo/25" value={openForm.data.opening_balance} onChange={(e) => openForm.setData('opening_balance', e.target.value)} />
                                <InputError message={openForm.errors.opening_balance} />
                            </div>
                            <div>
                                <InputLabel htmlFor="opening_notes" value="Notas" />
                                <input id="opening_notes" className="mt-1 block w-full rounded-md border-plomo/25" value={openForm.data.opening_notes} onChange={(e) => openForm.setData('opening_notes', e.target.value)} />
                            </div>
                            <div className="sm:col-span-2"><PrimaryButton disabled={openForm.processing}>Abrir caja</PrimaryButton></div>
                        </form>
                    )}
                </Card>
                <TableContainer title="Historial" description={`${cash_registers.data.length} registros`}>
                    <table className="min-w-full text-left text-sm">
                        <thead><tr><th className="px-4 py-2">Fecha</th><th className="px-4 py-2">Usuario</th><th className="px-4 py-2">Estado</th><th className="px-4 py-2">Apertura</th><th className="px-4 py-2">Cierre</th></tr></thead>
                        <tbody>
                            {cash_registers.data.map((row) => (
                                <tr key={row.id} className="border-t border-plomo/10">
                                    <td className="px-4 py-2">{row.business_date}</td>
                                    <td className="px-4 py-2">{row.user?.name ?? '—'}</td>
                                    <td className="px-4 py-2">{row.status}</td>
                                    <td className="px-4 py-2">S/ {Number(row.opening_balance).toFixed(2)}</td>
                                    <td className="px-4 py-2">{row.closing_balance ? `S/ ${Number(row.closing_balance).toFixed(2)}` : '—'}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </TableContainer>
            </PageContainer>
        </IntranetLayout>
    );
}

