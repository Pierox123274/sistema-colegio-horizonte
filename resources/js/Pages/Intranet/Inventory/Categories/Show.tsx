import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type P = PageProps<{
    category: {
        id: number;
        code: string;
        name: string;
        description: string | null;
        is_active: boolean;
        products_count: number;
    };
    products: Array<{
        id: number;
        code: string;
        name: string;
        current_stock: string;
        minimum_stock: string;
        is_active: boolean;
    }>;
}>;

export default function InventoryCategoryShow() {
    const { category, products, auth } = usePage<P>().props;
    const canManage = auth.user?.roles?.includes('Administrador') ?? false;

    return (
        <IntranetLayout title={category.code}>
            <Head title={`Categoría ${category.code}`} />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Inventario' },
                        {
                            label: 'Categorías',
                            href: route('intranet.inventory.categories.index'),
                        },
                        { label: category.code },
                    ]}
                />
                <SectionTitle
                    title={`${category.code} - ${category.name}`}
                    description="Detalle de categoría e inventario asociado."
                    actions={
                        <div className="flex gap-3">
                            {canManage ? (
                                <Link
                                    href={route(
                                        'intranet.inventory.categories.edit',
                                        category.id,
                                    )}
                                    className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                                >
                                    Editar
                                </Link>
                            ) : null}
                            <Link
                                href={route('intranet.inventory.categories.index')}
                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Listado
                            </Link>
                        </div>
                    }
                />
                <Card className="mb-6">
                    <dl className="grid gap-3 sm:grid-cols-2">
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Estado
                            </dt>
                            <dd className="mt-1">
                                <span
                                    className={`inline-flex rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ${
                                        category.is_active
                                            ? 'bg-emerald-50 text-emerald-900 ring-emerald-200'
                                            : 'bg-plomo/10 text-plomo ring-plomo/25'
                                    }`}
                                >
                                    {category.is_active
                                        ? 'Activa'
                                        : 'Inactiva'}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Productos asociados
                            </dt>
                            <dd className="mt-1 text-sm font-medium text-navy-900">
                                {category.products_count}
                            </dd>
                        </div>
                        <div className="sm:col-span-2">
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Descripción
                            </dt>
                            <dd className="mt-1 text-sm text-plomo">
                                {category.description ?? 'Sin descripción'}
                            </dd>
                        </div>
                    </dl>
                </Card>

                <TableContainer
                    title="Productos de la categoría"
                    description="Control de stock dentro de la categoría."
                >
                    <table className="min-w-full text-left text-sm">
                        <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo">
                            <tr>
                                <th className="px-4 py-3">Código</th>
                                <th className="px-4 py-3">Nombre</th>
                                <th className="px-4 py-3">Stock</th>
                                <th className="px-4 py-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-plomo/10">
                            {products.map((product) => {
                                const isLow =
                                    Number(product.current_stock) <=
                                    Number(product.minimum_stock);
                                return (
                                    <tr key={product.id}>
                                        <td className="px-4 py-3 font-mono text-xs">
                                            {product.code}
                                        </td>
                                        <td className="px-4 py-3 text-sm font-medium text-navy-900">
                                            {product.name}
                                        </td>
                                        <td className="px-4 py-3">
                                            <span
                                                className={`text-xs font-semibold ${
                                                    isLow
                                                        ? 'text-amber-700'
                                                        : 'text-navy-900'
                                                }`}
                                            >
                                                {Number(
                                                    product.current_stock,
                                                ).toFixed(2)}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-xs">
                                            {product.is_active
                                                ? 'Activo'
                                                : 'Inactivo'}
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </TableContainer>
            </PageContainer>
        </IntranetLayout>
    );
}

