import { Card } from '@/Components/Intranet/Card';
import { EmptyState } from '@/Components/Intranet/EmptyState';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { Filter, Package, Pencil, Plus, Trash2 } from 'lucide-react';
import { FormEvent, useState } from 'react';

type Row = {
    id: number;
    code: string;
    name: string;
    type: string;
    default_amount: string;
    is_active: boolean;
};

type PaginatorLink = { url: string | null; label: string; active: boolean };
type LaravelPaginator = { data: Row[]; links: PaginatorLink[] };

const TYPE_LABEL: Record<string, string> = {
    matricula: 'Matrícula',
    pension: 'Pensión',
    uniforme: 'Uniforme',
    libro: 'Libro',
    otro: 'Otro',
};

type Page = PageProps<{
    concepts: LaravelPaginator;
    filters: { search: string; type: string; is_active: string };
    catalog: { types: SelectOption[] };
}>;

export default function PaymentConceptsIndex() {
    const { concepts, filters, catalog, flash } = usePage<Page>().props;
    const [search, setSearch] = useState(String(filters.search ?? ''));
    const [type, setType] = useState(String(filters.type ?? ''));
    const [isActive, setIsActive] = useState(String(filters.is_active ?? ''));

    const apply = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('intranet.payment-concepts.index'),
            {
                search: search || undefined,
                type: type || undefined,
                is_active: isActive !== '' ? isActive : undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const rows = concepts.data ?? [];

    const destroy = (id: number, label: string) => {
        if (
            !confirm(`¿Eliminar el concepto «${label}»? Solo si no tiene uso.`)
        ) {
            return;
        }
        router.delete(route('intranet.payment-concepts.destroy', id));
    };

    return (
        <IntranetLayout title="Conceptos de pago">
            <Head title="Conceptos de pago — Horizonte" />

            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Finanzas' },
                        { label: 'Conceptos de pago' },
                    ]}
                />

                {flash?.success ? (
                    <div className="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        {flash.success}
                    </div>
                ) : null}
                {flash?.error ? (
                    <div className="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                        {flash.error}
                    </div>
                ) : null}

                <SectionTitle
                    title="Conceptos de pago"
                    description="Catálogo institucional para pensiones y cobros."
                    actions={
                        <Link
                            href={route('intranet.payment-concepts.create')}
                            className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-navy-950"
                        >
                            <Plus className="h-4 w-4" />
                            Nuevo concepto
                        </Link>
                    }
                />

                <Card className="mb-6">
                    <form
                        onSubmit={apply}
                        className="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-end"
                    >
                        <div className="min-w-[160px] flex-1">
                            <label className="block text-xs font-semibold uppercase text-plomo">
                                Buscar
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
                                Tipo
                            </label>
                            <select
                                value={type}
                                onChange={(e) => setType(e.target.value)}
                                className="mt-1 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">Todos</option>
                                {catalog.types.map((t) => (
                                    <option key={t.value} value={t.value}>
                                        {t.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-semibold uppercase text-plomo">
                                Activo
                            </label>
                            <select
                                value={isActive}
                                onChange={(e) => setIsActive(e.target.value)}
                                className="mt-1 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">Todos</option>
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <button
                            type="submit"
                            className="inline-flex items-center gap-2 rounded-lg border border-plomo/20 bg-white px-4 py-2 text-sm font-semibold text-navy-900 shadow-sm"
                        >
                            <Filter className="h-4 w-4" />
                            Filtrar
                        </button>
                    </form>
                </Card>

                <TableContainer
                    title="Listado"
                    description={`${rows.length} en esta página.`}
                >
                    {rows.length === 0 ? (
                        <div className="p-6">
                            <EmptyState
                                icon={Package}
                                title="Sin conceptos"
                                description="Cree el primer concepto de cobro."
                                action={
                                    <Link
                                        href={route(
                                            'intranet.payment-concepts.create',
                                        )}
                                        className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                                    >
                                        <Plus className="h-4 w-4" />
                                        Nuevo concepto
                                    </Link>
                                }
                            />
                        </div>
                    ) : (
                        <table className="min-w-full text-left text-sm">
                            <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo">
                                <tr>
                                    <th className="px-4 py-3">Código</th>
                                    <th className="px-4 py-3">Nombre</th>
                                    <th className="hidden px-4 py-3 md:table-cell">
                                        Tipo
                                    </th>
                                    <th className="px-4 py-3">Monto ref.</th>
                                    <th className="px-4 py-3">Estado</th>
                                    <th className="px-4 py-3 text-right">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-plomo/10">
                                {rows.map((r) => (
                                    <tr key={r.id} className="hover:bg-navy-50/40">
                                        <td className="px-4 py-3 font-mono text-xs font-semibold">
                                            {r.code}
                                        </td>
                                        <td className="px-4 py-3 font-medium text-navy-900">
                                            {r.name}
                                        </td>
                                        <td className="hidden px-4 py-3 text-xs md:table-cell">
                                            {TYPE_LABEL[r.type] ?? r.type}
                                        </td>
                                        <td className="px-4 py-3 font-mono text-xs">
                                            S/{' '}
                                            {Number(r.default_amount).toFixed(2)}
                                        </td>
                                        <td className="px-4 py-3">
                                            {r.is_active ? (
                                                <span className="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-900 ring-1 ring-emerald-200">
                                                    Activo
                                                </span>
                                            ) : (
                                                <span className="rounded-full bg-plomo/10 px-2 py-0.5 text-xs text-plomo">
                                                    Inactivo
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <Link
                                                href={route(
                                                    'intranet.payment-concepts.show',
                                                    r.id,
                                                )}
                                                className="mr-3 text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                                            >
                                                Ver
                                            </Link>
                                            <Link
                                                href={route(
                                                    'intranet.payment-concepts.edit',
                                                    r.id,
                                                )}
                                                className="mr-3 inline-flex items-center gap-1 text-sm font-semibold text-navy-900"
                                            >
                                                <Pencil className="h-3.5 w-3.5" />
                                                Editar
                                            </Link>
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    destroy(r.id, r.name)
                                                }
                                                className="inline-flex items-center gap-1 text-sm font-semibold text-rose-700"
                                            >
                                                <Trash2 className="h-3.5 w-3.5" />
                                                Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </TableContainer>

                {concepts.links?.length > 3 ? (
                    <nav className="mt-6 flex flex-wrap justify-center gap-1">
                        {concepts.links.map((link, i) =>
                            link.url ? (
                                <button
                                    key={i}
                                    type="button"
                                    className={`rounded-md px-3 py-1.5 text-sm ${
                                        link.active
                                            ? 'bg-navy-900 font-semibold text-white'
                                            : 'border border-plomo/20 bg-white'
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
