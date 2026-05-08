import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { FormEvent, useState } from 'react';

type P = PageProps<{
    movements: { data: Array<{ id: number; type: string; amount: string; moved_at: string; description: string | null; sale?: { sale_code: string }; cash_register?: { id: number; business_date: string; user?: { name: string } } }> };
    filters: { type: string; cash_register_id: string };
    catalog: { types: Array<{ value: string; label: string }> };
}>;

export default function CashMovementsIndex() {
    const { movements, filters, catalog } = usePage<P>().props;
    const [type, setType] = useState(filters.type ?? '');
    const [cashRegisterId, setCashRegisterId] = useState(filters.cash_register_id ?? '');
    const apply = (e: FormEvent) => {
        e.preventDefault();
        router.get(route('intranet.sales.cash-movements.index'), { type: type || undefined, cash_register_id: cashRegisterId || undefined }, { preserveState: true, replace: true });
    };
    return (
        <IntranetLayout title="Movimientos de caja">
            <Head title="Movimientos de caja" />
            <PageContainer>
                <SectionTitle title="Movimientos de caja" description="Apertura, ventas, anulaciones y cierre." />
                <form onSubmit={apply} className="mb-4 flex gap-3">
                    <select value={type} onChange={(e) => setType(e.target.value)} className="rounded-md border-plomo/25">
                        <option value="">Todos</option>
                        {catalog.types.map((t) => <option key={t.value} value={t.value}>{t.label}</option>)}
                    </select>
                    <input value={cashRegisterId} onChange={(e) => setCashRegisterId(e.target.value)} placeholder="ID caja" className="rounded-md border-plomo/25" />
                    <button type="submit" className="rounded border px-3">Filtrar</button>
                </form>
                <TableContainer title="Movimientos" description={`${movements.data.length} registros`}>
                    <table className="min-w-full text-left text-sm">
                        <thead><tr><th className="px-4 py-2">Fecha</th><th className="px-4 py-2">Tipo</th><th className="px-4 py-2">Monto</th><th className="px-4 py-2">Caja</th><th className="px-4 py-2">Venta</th><th className="px-4 py-2">Descripción</th></tr></thead>
                        <tbody>{movements.data.map((m) => <tr key={m.id} className="border-t border-plomo/10"><td className="px-4 py-2">{m.moved_at?.slice(0, 16)?.replace('T', ' ')}</td><td className="px-4 py-2">{m.type}</td><td className="px-4 py-2">S/ {Number(m.amount).toFixed(2)}</td><td className="px-4 py-2">{m.cash_register ? `#${m.cash_register.id} (${m.cash_register.business_date})` : '—'}</td><td className="px-4 py-2">{m.sale?.sale_code ?? '—'}</td><td className="px-4 py-2">{m.description ?? '—'}</td></tr>)}</tbody>
                    </table>
                </TableContainer>
            </PageContainer>
        </IntranetLayout>
    );
}

