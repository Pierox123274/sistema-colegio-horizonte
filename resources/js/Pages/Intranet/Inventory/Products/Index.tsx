import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { Filter, Plus, TriangleAlert } from 'lucide-react';
import { FormEvent, useMemo, useState } from 'react';

type ProductRow = {
    id: number;
    code: string;
    name: string;
    product_type: string;
    size: string;
    unit: string;
    current_stock: string;
    minimum_stock: string;
    is_active: boolean;
    category?: { code: string; name: string };
    inventory_movements_count: number;
};

type PaginatorLink = { url: string | null; label: string; active: boolean };
type Paginator = { data: ProductRow[]; links: PaginatorLink[] };

type P = PageProps<{
    products: Paginator;
    filters: {
        search: string;
        category_id: string;
        product_type: string;
        size: string;
        is_active: string;
        low_stock: boolean;
    };
    catalog: {
        categories: SelectOption[];
        product_types: SelectOption[];
        sizes: SelectOption[];
    };
}>;

export default function InventoryProductsIndex() {
    const { products, filters, catalog, auth } = usePage<P>().props;
    const [search, setSearch] = useState(filters.search ?? '');
    const [categoryId, setCategoryId] = useState(filters.category_id ?? '');
    const [productType, setProductType] = useState(filters.product_type ?? '');
    const [size, setSize] = useState(filters.size ?? '');
    const [isActive, setIsActive] = useState(filters.is_active ?? '');
    const [lowStock, setLowStock] = useState(Boolean(filters.low_stock));

    const canManage = useMemo(
        () => auth.user?.roles?.includes('Administrador') ?? false,
        [auth.user?.roles],
    );

    const apply = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('intranet.inventory.products.index'),
            {
                search: search || undefined,
                category_id: categoryId || undefined,
                product_type: productType || undefined,
                size: size || undefined,
                is_active: isActive || undefined,
                low_stock: lowStock || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    return (
        <IntranetLayout title="Inventario - Productos">
            <Head title="Inventario - Productos" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Inventario' },
                        { label: 'Productos' },
                    ]}
                />
                <SectionTitle
                    title="Productos de inventario"
                    description="Control de stock, precios y estado de productos."
                    actions={
                        canManage ? (
                            <Link
                                href={route('intranet.inventory.products.create')}
                                className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                            >
                                <Plus className="h-4 w-4" />
                                Nuevo producto
                            </Link>
                        ) : undefined
                    }
                />

                <Card className="mb-6">
                    <form
                        onSubmit={apply}
                        className="grid gap-4 md:grid-cols-[1fr_auto_auto_auto_auto_auto_auto]"
                    >
                        <div>
                            <label className="block text-xs font-semibold uppercase text-plomo">
                                Buscar
                            </label>
                            <input
                                type="search"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"
                                placeholder="Código o nombre"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-semibold uppercase text-plomo">
                                Categoría
                            </label>
                            <select
                                value={categoryId}
                                onChange={(e) => setCategoryId(e.target.value)}
                                className="mt-1 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">Todas</option>
                                {catalog.categories.map((c) => (
                                    <option key={c.value} value={c.value}>
                                        {c.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-semibold uppercase text-plomo">
                                Tipo
                            </label>
                            <select
                                value={productType}
                                onChange={(e) =>
                                    setProductType(e.target.value)
                                }
                                className="mt-1 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">Todos</option>
                                {catalog.product_types.map((item) => (
                                    <option
                                        key={item.value}
                                        value={item.value}
                                    >
                                        {item.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-semibold uppercase text-plomo">
                                Talla
                            </label>
                            <select
                                value={size}
                                onChange={(e) => setSize(e.target.value)}
                                className="mt-1 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">Todas</option>
                                {catalog.sizes.map((item) => (
                                    <option
                                        key={item.value}
                                        value={item.value}
                                    >
                                        {item.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-semibold uppercase text-plomo">
                                Estado
                            </label>
                            <select
                                value={isActive}
                                onChange={(e) => setIsActive(e.target.value)}
                                className="mt-1 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">Todos</option>
                                <option value="1">Activos</option>
                                <option value="0">Inactivos</option>
                            </select>
                        </div>
                        <label className="flex items-end gap-2 pb-2 text-sm font-medium text-navy-900">
                            <input
                                type="checkbox"
                                checked={lowStock}
                                onChange={(e) => setLowStock(e.target.checked)}
                            />
                            Solo stock bajo
                        </label>
                        <div className="flex items-end">
                            <button
                                type="submit"
                                className="inline-flex items-center gap-2 rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold"
                            >
                                <Filter className="h-4 w-4" />
                                Filtrar
                            </button>
                        </div>
                    </form>
                </Card>

                <TableContainer
                    title="Listado de productos"
                    description={`${products.data.length} registros en esta página.`}
                >
                    <table className="min-w-full text-left text-sm">
                        <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo">
                            <tr>
                                <th className="px-4 py-3">Código</th>
                                <th className="px-4 py-3">Producto</th>
                                <th className="px-4 py-3">Categoría</th>
                                <th className="px-4 py-3">Stock</th>
                                <th className="px-4 py-3">Estado</th>
                                <th className="px-4 py-3 text-right">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-plomo/10">
                            {products.data.map((row) => {
                                const isLow =
                                    Number(row.current_stock) <=
                                    Number(row.minimum_stock);
                                return (
                                    <tr key={row.id}>
                                        <td className="px-4 py-3 font-mono text-xs">
                                            {row.code}
                                        </td>
                                        <td className="px-4 py-3">
                                            <p className="font-medium text-navy-900">
                                                {row.name}
                                            </p>
                                            <p className="text-xs text-plomo">
                                                Tipo: {row.product_type} - Talla:{' '}
                                                {row.size} - Unidad: {row.unit} -
                                                Movimientos: {row.inventory_movements_count}
                                            </p>
                                        </td>
                                        <td className="px-4 py-3 text-xs">
                                            {row.category
                                                ? `${row.category.code} - ${row.category.name}`
                                                : '—'}
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="flex items-center gap-2 text-xs">
                                                {isLow ? (
                                                    <TriangleAlert className="h-4 w-4 text-amber-600" />
                                                ) : null}
                                                <span
                                                    className={
                                                        isLow
                                                            ? 'font-semibold text-amber-700'
                                                            : 'font-semibold text-navy-900'
                                                    }
                                                >
                                                    {Number(
                                                        row.current_stock,
                                                    ).toFixed(2)}
                                                </span>
                                                <span className="text-plomo">
                                                    min{' '}
                                                    {Number(
                                                        row.minimum_stock,
                                                    ).toFixed(2)}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="px-4 py-3">
                                            <span
                                                className={`inline-flex rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ${
                                                    row.is_active
                                                        ? 'bg-emerald-50 text-emerald-900 ring-emerald-200'
                                                        : 'bg-plomo/10 text-plomo ring-plomo/25'
                                                }`}
                                            >
                                                {row.is_active
                                                    ? 'Activo'
                                                    : 'Inactivo'}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <div className="flex justify-end gap-3">
                                                <Link
                                                    href={route(
                                                        'intranet.inventory.products.show',
                                                        row.id,
                                                    )}
                                                    className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                                                >
                                                    Ver
                                                </Link>
                                                {canManage && row.is_active ? (
                                                    <button
                                                        type="button"
                                                        onClick={() => {
                                                            if (
                                                                window.confirm(
                                                                    '¿Desactivar producto?',
                                                                )
                                                            ) {
                                                                router.post(
                                                                    route(
                                                                        'intranet.inventory.products.deactivate',
                                                                        row.id,
                                                                    ),
                                                                );
                                                            }
                                                        }}
                                                        className="text-sm font-semibold text-amber-700 underline-offset-2 hover:underline"
                                                    >
                                                        Desactivar
                                                    </button>
                                                ) : null}
                                            </div>
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

