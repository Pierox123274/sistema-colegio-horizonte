import { Card } from '@/Components/Intranet/Card';
import { EmptyState } from '@/Components/Intranet/EmptyState';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { CalendarRange, Pencil, Plus } from 'lucide-react';

type YearRow = {
    id: number;
    name: string;
    year: number;
    starts_at: string;
    ends_at: string;
    is_active: boolean;
};

type PaginatorLink = { url: string | null; label: string; active: boolean };

type LaravelPaginator = {
    data: YearRow[];
    links: PaginatorLink[];
};

type IndexPageProps = PageProps<{
    years: LaravelPaginator;
    permissions: { manage: boolean };
}>;

export default function AcademicYearsIndex() {
    const { years, permissions, flash } = usePage<IndexPageProps>().props;

    const rows = years.data ?? [];

    return (
        <IntranetLayout title="Años académicos">
            <Head title="Años académicos — Horizonte" />

            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Años académicos' }]} />

                {flash?.success ? (
                    <div
                        className="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900"
                        role="status"
                    >
                        {flash.success}
                    </div>
                ) : null}

                <SectionTitle
                    title="Años académicos"
                    description="Solo puede haber un año marcado como activo; al activar uno, los demás se desactivan."
                    actions={
                        permissions.manage ? (
                            <Link
                                href={route('intranet.academic-years.create')}
                                className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                            >
                                <Plus className="h-4 w-4" aria-hidden />
                                Nuevo año
                            </Link>
                        ) : null
                    }
                />

                <TableContainer
                    title="Listado"
                    description={`${rows.length} registros en esta página.`}
                >
                    {rows.length === 0 ? (
                        <div className="p-6">
                            <EmptyState
                                icon={CalendarRange}
                                title="Sin años académicos"
                                description="Cree el primer año para habilitar matrículas."
                                action={
                                    permissions.manage ? (
                                        <Link
                                            href={route(
                                                'intranet.academic-years.create',
                                            )}
                                            className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                                        >
                                            <Plus className="h-4 w-4" />
                                            Nuevo año
                                        </Link>
                                    ) : null
                                }
                            />
                        </div>
                    ) : (
                        <table className="min-w-full text-left text-sm">
                            <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase tracking-wide text-plomo">
                                <tr>
                                    <th className="px-4 py-3 sm:px-6">Nombre</th>
                                    <th className="hidden px-4 py-3 md:table-cell sm:px-6">
                                        Año
                                    </th>
                                    <th className="hidden px-4 py-3 lg:table-cell sm:px-6">
                                        Vigencia
                                    </th>
                                    <th className="px-4 py-3 sm:px-6">Estado</th>
                                    <th className="px-4 py-3 text-right sm:px-6">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-plomo/10">
                                {rows.map((row) => (
                                    <tr
                                        key={row.id}
                                        className="bg-white hover:bg-navy-50/40"
                                    >
                                        <td className="px-4 py-3 font-medium text-navy-900 sm:px-6">
                                            {row.name}
                                        </td>
                                        <td className="hidden px-4 py-3 font-mono text-xs md:table-cell sm:px-6">
                                            {row.year}
                                        </td>
                                        <td className="hidden px-4 py-3 text-xs lg:table-cell sm:px-6">
                                            {row.starts_at?.slice(0, 10)} →{' '}
                                            {row.ends_at?.slice(0, 10)}
                                        </td>
                                        <td className="px-4 py-3 sm:px-6">
                                            {row.is_active ? (
                                                <span className="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-900 ring-1 ring-emerald-200">
                                                    Activo
                                                </span>
                                            ) : (
                                                <span className="inline-flex rounded-full bg-plomo/10 px-2.5 py-0.5 text-xs font-semibold text-plomo ring-1 ring-plomo/20">
                                                    Inactivo
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-4 py-3 text-right sm:px-6">
                                            {permissions.manage ? (
                                                <Link
                                                    href={route(
                                                        'intranet.academic-years.edit',
                                                        row.id,
                                                    )}
                                                    className="inline-flex items-center gap-1 text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                                                >
                                                    <Pencil className="h-3.5 w-3.5" />
                                                    Editar
                                                </Link>
                                            ) : (
                                                '—'
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </TableContainer>

                {years.links && years.links.length > 3 ? (
                    <nav
                        className="mt-6 flex flex-wrap justify-center gap-1"
                        aria-label="Paginación"
                    >
                        {years.links.map((link, i) => {
                            if (!link.url) {
                                return (
                                    <span
                                        key={i}
                                        className="rounded-md px-3 py-1.5 text-sm text-plomo"
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                );
                            }
                            return (
                                <button
                                    key={i}
                                    type="button"
                                    className={`rounded-md px-3 py-1.5 text-sm ${
                                        link.active
                                            ? 'bg-navy-900 font-semibold text-white'
                                            : 'border border-plomo/20 bg-white text-navy-900 hover:bg-navy-50'
                                    }`}
                                    onClick={() => router.visit(link.url!)}
                                    dangerouslySetInnerHTML={{
                                        __html: link.label,
                                    }}
                                />
                            );
                        })}
                    </nav>
                ) : null}

                <Card className="mt-8">
                    <p className="text-sm text-plomo">
                        Desde{' '}
                        <Link
                            href={route('intranet.enrollments.index')}
                            className="font-semibold text-navy-900 underline-offset-2 hover:underline"
                        >
                            Matrículas
                        </Link>{' '}
                        puede registrar alumnos por año académico.
                    </p>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
