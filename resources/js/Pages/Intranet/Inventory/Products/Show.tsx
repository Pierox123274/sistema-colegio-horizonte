import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { TriangleAlert } from 'lucide-react';

type P = PageProps<{
    product: {
        id: number;
        code: string;
        name: string;
        description: string | null;
        product_type: string;
        size: string;
        color: string | null;
        gender_target: string;
        unit: string;
        purchase_price: string;
        sale_price: string;
        current_stock: string;
        minimum_stock: string;
        is_active: boolean;
        category?: { id: number; code: string; name: string };
        inventory_movements: Array<{
            id: number;
            type: string;
            quantity: string;
            previous_stock: string;
            new_stock: string;
            reason: string;
            created_at: string;
            created_by_user?: { name: string };
            status: string;
        }>;
    };
    is_low_stock: boolean;
}>;

export default function InventoryProductShow() {
    const { product, is_low_stock, auth } = usePage<P>().props;
    const canManage = auth.user?.roles?.includes('Administrador') ?? false;

    return (
        <IntranetLayout title={product.code}>
            <Head title={`Producto ${product.code}`} />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Inventario' },
                        {
                            label: 'Productos',
                            href: route('intranet.inventory.products.index'),
                        },
                        { label: product.code },
                    ]}
                />
                <SectionTitle
                    title={`${product.code} - ${product.name}`}
                    description="Ficha del producto y últimos movimientos de stock."
                    actions={
                        <div className="flex gap-3">
                            {canManage ? (
                                <Link
                                    href={route(
                                        'intranet.inventory.products.edit',
                                        product.id,
                                    )}
                                    className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                                >
                                    Editar
                                </Link>
                            ) : null}
                            <Link
                                href={route('intranet.inventory.products.index')}
                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Listado
                            </Link>
                        </div>
                    }
                />
                <Card className="mb-6">
                    <dl className="grid gap-4 md:grid-cols-3">
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Categoría
                            </dt>
                            <dd className="mt-1 text-sm font-medium text-navy-900">
                                {product.category
                                    ? `${product.category.code} - ${product.category.name}`
                                    : '—'}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Tipo / talla
                            </dt>
                            <dd className="mt-1 text-sm text-navy-900">
                                {product.product_type} / {product.size}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Color / género
                            </dt>
                            <dd className="mt-1 text-sm text-navy-900">
                                {product.color ?? '—'} / {product.gender_target}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Precio compra / venta
                            </dt>
                            <dd className="mt-1 text-sm text-navy-900">
                                S/ {Number(product.purchase_price).toFixed(2)} /{' '}
                                {Number(product.sale_price).toFixed(2)}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Estado
                            </dt>
                            <dd className="mt-1">
                                <span
                                    className={`inline-flex rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ${
                                        product.is_active
                                            ? 'bg-emerald-50 text-emerald-900 ring-emerald-200'
                                            : 'bg-plomo/10 text-plomo ring-plomo/25'
                                    }`}
                                >
                                    {product.is_active
                                        ? 'Activo'
                                        : 'Inactivo'}
                                </span>
                            </dd>
                        </div>
                        <div className="md:col-span-3">
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Stock
                            </dt>
                            <dd className="mt-1 flex items-center gap-2 text-sm">
                                {is_low_stock ? (
                                    <TriangleAlert className="h-4 w-4 text-amber-600" />
                                ) : null}
                                <span
                                    className={
                                        is_low_stock
                                            ? 'font-semibold text-amber-700'
                                            : 'font-semibold text-navy-900'
                                    }
                                >
                                    Actual: {Number(product.current_stock).toFixed(2)}{' '}
                                    {product.unit}
                                </span>
                                <span className="text-plomo">
                                    Mínimo:{' '}
                                    {Number(product.minimum_stock).toFixed(2)}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </Card>

                <TableContainer
                    title="Últimos movimientos"
                    description="Trazabilidad reciente del producto."
                >
                    <table className="min-w-full text-left text-sm">
                        <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo">
                            <tr>
                                <th className="px-4 py-3">Fecha</th>
                                <th className="px-4 py-3">Tipo</th>
                                <th className="px-4 py-3">Estado</th>
                                <th className="px-4 py-3">Cantidad</th>
                                <th className="px-4 py-3">Stock</th>
                                <th className="px-4 py-3">Motivo</th>
                                <th className="px-4 py-3">Usuario</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-plomo/10">
                            {product.inventory_movements.map((m) => (
                                <tr key={m.id}>
                                    <td className="px-4 py-3 text-xs">
                                        {m.created_at
                                            ?.slice(0, 16)
                                            ?.replace('T', ' ')}
                                    </td>
                                    <td className="px-4 py-3 text-xs uppercase">
                                        {m.type}
                                    </td>
                                    <td className="px-4 py-3 text-xs">
                                        {m.status === 'anulado'
                                            ? 'Anulado'
                                            : 'Registrado'}
                                    </td>
                                    <td className="px-4 py-3 font-mono text-xs">
                                        {Number(m.quantity).toFixed(2)}
                                    </td>
                                    <td className="px-4 py-3 font-mono text-xs">
                                        {Number(m.previous_stock).toFixed(2)} →{' '}
                                        {Number(m.new_stock).toFixed(2)}
                                    </td>
                                    <td className="px-4 py-3 text-xs">
                                        {m.reason}
                                    </td>
                                    <td className="px-4 py-3 text-xs">
                                        {m.created_by_user?.name ?? '—'}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </TableContainer>
            </PageContainer>
        </IntranetLayout>
    );
}

