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

type MovementRow = {
    id: number;
    type: string;
    status: string;
    quantity: string;
    previous_stock: string;
    new_stock: string;
    reason: string;
    created_at: string;
    product?: {
        id: number;
        code: string;
        name: string;
        current_stock: string;
        minimum_stock: string;
        category?: { code: string; name: string };
    };
    created_by_user?: { name: string };
};

type PaginatorLink = { url: string | null; label: string; active: boolean };
type Paginator = { data: MovementRow[]; links: PaginatorLink[] };

type P = PageProps<{
    movements: Paginator;
    filters: {
        search: string;
        type: string;
        status: string;
        category_id: string;
        low_stock: boolean;
    };
    catalog: {
        types: SelectOption[];
        statuses: SelectOption[];
        categories: SelectOption[];
    };
    stats: {
        total_products: number;
        low_stock_products: number;
        active_products: number;
        total_movements: number;
    };
}>;

export default function InventoryMovementsIndex() {
    const { movements, filters, catalog, stats, auth } = usePage<P>().props;
    const [search, setSearch] = useState(filters.search ?? '');
    const [type, setType] = useState(filters.type ?? '');
    const [status, setStatus] = useState(filters.status ?? '');
    const [categoryId, setCategoryId] = useState(filters.category_id ?? '');
    const [lowStock, setLowStock] = useState(Boolean(filters.low_stock));

    const canManage = useMemo(
        () => auth.user?.roles?.includes('Administrador') ?? false,
        [auth.user?.roles],
    );

    const apply = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('intranet.inventory.movements.index'),
            {
                search: search || undefined,
                type: type || undefined,
                status: status || undefined,
                category_id: categoryId || undefined,
                low_stock: lowStock || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    return (
        <IntranetLayout title="Inventario - Movimientos">
            <Head title="Inventario - Movimientos" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Inventario' },
                        { label: 'Movimientos' },
                    ]}
                />
                <SectionTitle
                    title="Movimientos de inventario"
                    description="Entradas, salidas y ajustes con trazabilidad completa."
                    actions={
                        canManage ? (
                            <Link
                                href={route(
                                    'intranet.inventory.movements.create',
                                )}
                                className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                            >
                                <Plus className="h-4 w-4" />
                                Registrar movimiento
                            </Link>
                        ) : undefined
                    }
                />

                <div className="mb-6 grid gap-4 md:grid-cols-4">
                    <Card>
                        <p className="text-xs font-semibold uppercase text-plomo">
                            Productos
                        </p>
                        <p className="mt-1 text-2xl font-bold text-navy-900">
                            {stats.total_products}
                        </p>
                    </Card>
                    <Card>
                        <p className="text-xs font-semibold uppercase text-plomo">
                            Stock bajo
                        </p>
                        <p className="mt-1 text-2xl font-bold text-amber-700">
                            {stats.low_stock_products}
                        </p>
                    </Card>
                    <Card>
                        <p className="text-xs font-semibold uppercase text-plomo">
                            Productos activos
                        </p>
                        <p className="mt-1 text-2xl font-bold text-navy-900">
                            {stats.active_products}
                        </p>
                    </Card>
                    <Card>
                        <p className="text-xs font-semibold uppercase text-plomo">
                            Movimientos
                        </p>
                        <p className="mt-1 text-2xl font-bold text-navy-900">
                            {stats.total_movements}
                        </p>
                    </Card>
                </div>

                <Card className="mb-6">
                    <form
                        onSubmit={apply}
                        className="grid gap-4 md:grid-cols-[1fr_auto_auto_auto_auto_auto]"
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
                                placeholder="Producto o motivo"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-semibold uppercase text-plomo">
                                Tipo
                            </label>
                            <select
                                value={type}
                                onChange={(e) => setType(e.target.value)}
                                className="mt-1 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">Todos</option>
                                {catalog.types.map((item) => (
                                    <option key={item.value} value={item.value}>
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
                                value={status}
                                onChange={(e) => setStatus(e.target.value)}
                                className="mt-1 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">Todos</option>
                                {catalog.statuses.map((item) => (
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
                                Categoría
                            </label>
                            <select
                                value={categoryId}
                                onChange={(e) => setCategoryId(e.target.value)}
                                className="mt-1 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">Todas</option>
                                {catalog.categories.map((item) => (
                                    <option key={item.value} value={item.value}>
                                        {item.label}
                                    </option>
                                ))}
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
                    title="Historial de movimientos"
                    description={`${movements.data.length} registros en esta página.`}
                >
                    <table className="min-w-full text-left text-sm">
                        <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo">
                            <tr>
                                <th className="px-4 py-3">Fecha</th>
                                <th className="px-4 py-3">Producto</th>
                                <th className="px-4 py-3">Tipo</th>
                                <th className="px-4 py-3">Estado</th>
                                <th className="px-4 py-3">Cantidad</th>
                                <th className="px-4 py-3">Stock</th>
                                <th className="px-4 py-3">Usuario</th>
                                <th className="px-4 py-3 text-right">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-plomo/10">
                            {movements.data.map((m) => {
                                const isLow =
                                    Number(m.product?.current_stock ?? 0) <=
                                    Number(m.product?.minimum_stock ?? 0);
                                return (
                                    <tr key={m.id}>
                                        <td className="px-4 py-3 text-xs">
                                            {m.created_at
                                                ?.slice(0, 16)
                                                ?.replace('T', ' ')}
                                        </td>
                                        <td className="px-4 py-3">
                                            <p className="font-medium text-navy-900">
                                                {m.product?.name ?? '—'}
                                            </p>
                                            <p className="text-xs text-plomo">
                                                {m.product?.code}
                                            </p>
                                        </td>
                                        <td className="px-4 py-3 text-xs uppercase">
                                            {m.type}
                                        </td>
                                        <td className="px-4 py-3 text-xs">
                                            <span
                                                className={`inline-flex rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ${
                                                    m.status === 'anulado'
                                                        ? 'bg-plomo/10 text-plomo ring-plomo/25'
                                                        : 'bg-emerald-50 text-emerald-900 ring-emerald-200'
                                                }`}
                                            >
                                                {m.status === 'anulado'
                                                    ? 'Anulado'
                                                    : 'Registrado'}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 font-mono text-xs">
                                            {Number(m.quantity).toFixed(2)}
                                        </td>
                                        <td className="px-4 py-3 font-mono text-xs">
                                            {Number(m.previous_stock).toFixed(2)} →{' '}
                                            {Number(m.new_stock).toFixed(2)}
                                            {isLow ? (
                                                <span className="ml-2 inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-800">
                                                    <TriangleAlert className="h-3 w-3" />
                                                    Bajo
                                                </span>
                                            ) : null}
                                        </td>
                                        <td className="px-4 py-3 text-xs">
                                            {m.created_by_user?.name ?? '—'}
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <div className="flex justify-end gap-3">
                                                <Link
                                                    href={route(
                                                        'intranet.inventory.movements.show',
                                                        m.id,
                                                    )}
                                                    className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                                                >
                                                    Ver
                                                </Link>
                                                {canManage &&
                                                m.status !== 'anulado' ? (
                                                    <button
                                                        type="button"
                                                        onClick={() => {
                                                            if (
                                                                window.confirm(
                                                                    '¿Anular movimiento?',
                                                                )
                                                            ) {
                                                                router.post(
                                                                    route(
                                                                        'intranet.inventory.movements.cancel',
                                                                        m.id,
                                                                    ),
                                                                );
                                                            }
                                                        }}
                                                        className="text-sm font-semibold text-amber-700 underline-offset-2 hover:underline"
                                                    >
                                                        Anular
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

