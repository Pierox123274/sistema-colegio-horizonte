import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';

type P = PageProps<{ sale: { id: number; sale_code: string; status: string; total: string; sold_at: string; payment_method: string; student?: { first_name: string; last_name: string; code: string }; guardian?: { first_name: string; last_name: string }; items: Array<{ id: number; quantity: string; unit_price: string; subtotal: string; product?: { code: string; name: string; size: string; unit: string } }> } }>;

export default function SalesShow() {
    const { sale } = usePage<P>().props;
    return (
        <IntranetLayout title={sale.sale_code}>
            <Head title={`Venta ${sale.sale_code}`} />
            <PageContainer>
                <SectionTitle title={`Venta ${sale.sale_code}`} description="Detalle y comprobante." actions={<div className="flex gap-3"><Link href={route('intranet.sales.sales.index')} className="text-sm font-semibold text-navy-900 underline">Ver listado</Link><Link href={route('intranet.sales.sales.receipt', sale.id)} className="text-sm font-semibold text-navy-900 underline">Ver comprobante</Link><Link href={route('intranet.sales.sales.receipt.pdf', sale.id)} className="text-sm font-semibold text-navy-900 underline">Descargar PDF</Link><Link href={route('intranet.sales.sales.receipt.ticket', sale.id)} className="text-sm font-semibold text-navy-900 underline">Imprimir ticket</Link></div>} />
                <Card className="mb-4">
                    <p><strong>Estado:</strong> {sale.status}</p>
                    <p><strong>Método:</strong> {sale.payment_method}</p>
                    <p><strong>Fecha:</strong> {sale.sold_at?.slice(0, 16)?.replace('T', ' ')}</p>
                    <p><strong>Alumno:</strong> {sale.student ? `${sale.student.first_name} ${sale.student.last_name} (${sale.student.code})` : '—'}</p>
                    <p><strong>Apoderado:</strong> {sale.guardian ? `${sale.guardian.first_name} ${sale.guardian.last_name}` : '—'}</p>
                    <p><strong>Total:</strong> S/ {Number(sale.total).toFixed(2)}</p>
                    {sale.status !== 'anulada' ? (
                        <button type="button" className="mt-3 rounded border px-3 py-1 text-sm text-amber-700" onClick={() => { if (window.confirm('¿Anular venta?')) { router.post(route('intranet.sales.sales.cancel', sale.id)); } }}>Anular venta</button>
                    ) : null}
                </Card>
                <TableContainer title="Items" description={`${sale.items.length} productos`}>
                    <table className="min-w-full text-left text-sm">
                        <thead><tr><th className="px-4 py-2">Producto</th><th className="px-4 py-2">Cant.</th><th className="px-4 py-2">P. Unit</th><th className="px-4 py-2">Subtotal</th></tr></thead>
                        <tbody>{sale.items.map((item) => <tr key={item.id} className="border-t border-plomo/10"><td className="px-4 py-2">{item.product ? `${item.product.code} - ${item.product.name} (${item.product.size})` : '—'}</td><td className="px-4 py-2">{Number(item.quantity).toFixed(2)}</td><td className="px-4 py-2">S/ {Number(item.unit_price).toFixed(2)}</td><td className="px-4 py-2">S/ {Number(item.subtotal).toFixed(2)}</td></tr>)}</tbody>
                    </table>
                </TableContainer>
            </PageContainer>
        </IntranetLayout>
    );
}

