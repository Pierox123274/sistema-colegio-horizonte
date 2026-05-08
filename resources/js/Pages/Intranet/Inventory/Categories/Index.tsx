import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { Filter, Plus } from 'lucide-react';
import { FormEvent, useMemo, useState } from 'react';

type CategoryRow = {
    id: number;
    code: string;
    name: string;
    description: string | null;
    is_active: boolean;
    products_count: number;
};

type PaginatorLink = { url: string | null; label: string; active: boolean };
type Paginator = { data: CategoryRow[]; links: PaginatorLink[] };

type P = PageProps<{
    categories: Paginator;
    filters: { search: string; is_active: string };
}>;

export default function InventoryCategoriesIndex() {
    const { categories, filters, auth } = usePage<P>().props;
    const [search, setSearch] = useState(filters.search ?? '');
    const [isActive, setIsActive] = useState(filters.is_active ?? '');

    const canManage = useMemo(
        () => auth.user?.roles?.includes('Administrador') ?? false,
        [auth.user?.roles],
    );

    const apply = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('intranet.inventory.categories.index'),
            {
                search: search || undefined,
                is_active: isActive || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    return (
        <IntranetLayout title="Inventario - Categorías">
            <Head title="Inventario - Categorías" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Inventario' },
                        { label: 'Categorías' },
                    ]}
                />
                <SectionTitle
                    title="Categorías de productos"
                    description="Catálogo base para organizar productos de inventario."
                    actions={
                        canManage ? (
                            <Link
                                href={route(
                                    'intranet.inventory.categories.create',
                                )}
                                className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                            >
                                <Plus className="h-4 w-4" />
                                Nueva categoría
                            </Link>
                        ) : undefined
                    }
                />

                <Card className="mb-6">
                    <form
                        onSubmit={apply}
                        className="grid gap-4 md:grid-cols-[1fr_auto_auto]"
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
                    title="Listado de categorías"
                    description={`${categories.data.length} registros en esta página.`}
                >
                    <table className="min-w-full text-left text-sm">
                        <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo">
                            <tr>
                                <th className="px-4 py-3">Código</th>
                                <th className="px-4 py-3">Nombre</th>
                                <th className="px-4 py-3">Estado</th>
                                <th className="px-4 py-3">Productos</th>
                                <th className="px-4 py-3 text-right">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-plomo/10">
                            {categories.data.map((row) => (
                                <tr key={row.id}>
                                    <td className="px-4 py-3 font-mono text-xs">
                                        {row.code}
                                    </td>
                                    <td className="px-4 py-3">
                                        <p className="font-medium text-navy-900">
                                            {row.name}
                                        </p>
                                        {row.description ? (
                                            <p className="text-xs text-plomo">
                                                {row.description}
                                            </p>
                                        ) : null}
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
                                    <td className="px-4 py-3 text-xs">
                                        {row.products_count}
                                    </td>
                                    <td className="px-4 py-3 text-right">
                                        <div className="flex justify-end gap-3">
                                            <Link
                                                href={route(
                                                    'intranet.inventory.categories.show',
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
                                                                '¿Desactivar categoría?',
                                                            )
                                                        ) {
                                                            router.post(
                                                                route(
                                                                    'intranet.inventory.categories.deactivate',
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
                            ))}
                        </tbody>
                    </table>
                </TableContainer>
            </PageContainer>
        </IntranetLayout>
    );
}

