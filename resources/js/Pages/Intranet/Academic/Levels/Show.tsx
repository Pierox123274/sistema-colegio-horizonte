import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type GradeBrief = {
    id: number;
    code: string;
    name: string;
    order: number;
    is_active: boolean;
};

type LevelDetail = {
    id: number;
    code: string;
    name: string;
    description: string | null;
    is_active: boolean;
    grades_count?: number;
    grades?: GradeBrief[];
};

type ShowPageProps = PageProps<{
    level: LevelDetail;
    permissions: { manage: boolean };
}>;

export default function LevelsShow() {
    const { level, permissions, flash } = usePage<ShowPageProps>().props;

    const grades = level.grades ?? [];

    return (
        <IntranetLayout title={level.name}>
            <Head title={`${level.name} — Nivel — Horizonte`} />

            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        {
                            label: 'Niveles educativos',
                            href: route('intranet.academic.levels.index'),
                        },
                        { label: level.name },
                    ]}
                />

                {flash?.success ? (
                    <div
                        className="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900"
                        role="status"
                    >
                        {flash.success}
                    </div>
                ) : null}

                <SectionTitle
                    title={level.name}
                    description={
                        level.description ??
                        `Código ${level.code} · ${level.grades_count ?? grades.length} grados`
                    }
                    actions={
                        <div className="flex flex-wrap gap-2">
                            {permissions.manage ? (
                                <Link
                                    href={route(
                                        'intranet.academic.levels.edit',
                                        level.id,
                                    )}
                                    className="inline-flex items-center rounded-lg border border-plomo/20 bg-white px-4 py-2 text-sm font-semibold text-navy-900 shadow-sm transition hover:bg-navy-50"
                                >
                                    Editar nivel
                                </Link>
                            ) : null}
                            <Link
                                href={route('intranet.academic.grades.index', {
                                    educational_level_id: level.id,
                                })}
                                className="inline-flex items-center rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                            >
                                Ver grados filtrados
                            </Link>
                        </div>
                    }
                />

                <div className="mb-6 flex flex-wrap gap-3">
                    <span
                        className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 ${
                            level.is_active
                                ? 'bg-emerald-50 text-emerald-900 ring-emerald-200'
                                : 'bg-plomo/10 text-plomo ring-plomo/20'
                        }`}
                    >
                        {level.is_active ? 'Activo' : 'Inactivo'}
                    </span>
                    <span className="inline-flex rounded-full bg-navy-900/5 px-3 py-1 font-mono text-xs font-semibold text-navy-900 ring-1 ring-navy-900/10">
                        {level.code}
                    </span>
                </div>

                <Card className="mb-6">
                    <h2 className="text-sm font-bold uppercase tracking-wider text-navy-900">
                        Resumen
                    </h2>
                    <dl className="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Grados en este nivel
                            </dt>
                            <dd className="mt-1 text-lg font-bold text-navy-900">
                                {level.grades_count ?? grades.length}
                            </dd>
                        </div>
                    </dl>
                </Card>

                <TableContainer
                    title="Grados"
                    description="Relación descendente del nivel."
                >
                    {grades.length === 0 ? (
                        <p className="p-6 text-sm text-plomo">
                            No hay grados registrados todavía.
                        </p>
                    ) : (
                        <table className="min-w-full text-left text-sm">
                            <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase tracking-wide text-plomo">
                                <tr>
                                    <th className="px-4 py-3 sm:px-6">Orden</th>
                                    <th className="px-4 py-3 sm:px-6">Código</th>
                                    <th className="px-4 py-3 sm:px-6">Nombre</th>
                                    <th className="px-4 py-3 sm:px-6">Estado</th>
                                    <th className="px-4 py-3 text-right sm:px-6">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-plomo/10">
                                {grades.map((g) => (
                                    <tr
                                        key={g.id}
                                        className="bg-white hover:bg-navy-50/40"
                                    >
                                        <td className="px-4 py-3 text-navy-900 sm:px-6">
                                            {g.order}
                                        </td>
                                        <td className="px-4 py-3 font-mono text-xs font-semibold text-navy-900 sm:px-6">
                                            {g.code}
                                        </td>
                                        <td className="px-4 py-3 font-medium text-navy-900 sm:px-6">
                                            {g.name}
                                        </td>
                                        <td className="px-4 py-3 sm:px-6">
                                            <span
                                                className={`inline-flex rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ${
                                                    g.is_active
                                                        ? 'bg-emerald-50 text-emerald-900 ring-emerald-200'
                                                        : 'bg-plomo/10 text-plomo ring-plomo/20'
                                                }`}
                                            >
                                                {g.is_active ? 'Activo' : 'Inactivo'}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-right sm:px-6">
                                            <Link
                                                href={route(
                                                    'intranet.academic.grades.show',
                                                    g.id,
                                                )}
                                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                                            >
                                                Ver
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </TableContainer>
            </PageContainer>
        </IntranetLayout>
    );
}
