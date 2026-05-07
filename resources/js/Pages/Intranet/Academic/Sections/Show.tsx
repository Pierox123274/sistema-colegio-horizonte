import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type ClassroomBrief = {
    id: number;
    code: string;
    name: string;
    floor: string | null;
    capacity: number;
    is_active: boolean;
};

type SectionDetail = {
    id: number;
    code: string;
    name: string;
    capacity: number;
    is_active: boolean;
    classrooms_count?: number;
    grade?: {
        id: number;
        code: string;
        name: string;
        educational_level?: { id: number; code: string; name: string };
    };
    classrooms?: ClassroomBrief[];
};

type ShowPageProps = PageProps<{
    section: SectionDetail;
    permissions: { manage: boolean };
}>;

export default function SectionsShow() {
    const { section, permissions, flash } = usePage<ShowPageProps>().props;

    const classrooms = section.classrooms ?? [];

    return (
        <IntranetLayout title={section.name}>
            <Head title={`${section.name} — Sección — Horizonte`} />

            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        {
                            label: 'Secciones',
                            href: route('intranet.academic.sections.index'),
                        },
                        { label: section.name },
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
                    title={`Sección ${section.code}`}
                    description={section.name}
                    actions={
                        <div className="flex flex-wrap gap-2">
                            {permissions.manage ? (
                                <Link
                                    href={route(
                                        'intranet.academic.sections.edit',
                                        section.id,
                                    )}
                                    className="inline-flex items-center rounded-lg border border-plomo/20 bg-white px-4 py-2 text-sm font-semibold text-navy-900 shadow-sm transition hover:bg-navy-50"
                                >
                                    Editar sección
                                </Link>
                            ) : null}
                            <Link
                                href={route(
                                    'intranet.academic.classrooms.index',
                                    {
                                        section_id: section.id,
                                    },
                                )}
                                className="inline-flex items-center rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                            >
                                Ver aulas filtradas
                            </Link>
                            {section.grade ? (
                                <Link
                                    href={route(
                                        'intranet.academic.grades.show',
                                        section.grade.id,
                                    )}
                                    className="inline-flex items-center rounded-lg border border-plomo/20 bg-white px-4 py-2 text-sm font-semibold text-navy-900 shadow-sm transition hover:bg-navy-50"
                                >
                                    Ver grado
                                </Link>
                            ) : null}
                        </div>
                    }
                />

                <div className="mb-6 flex flex-wrap gap-3">
                    <span className="inline-flex rounded-full bg-navy-900/5 px-3 py-1 font-mono text-xs font-semibold text-navy-900 ring-1 ring-navy-900/10">
                        Cupo: {section.capacity}
                    </span>
                    <span
                        className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 ${
                            section.is_active
                                ? 'bg-emerald-50 text-emerald-900 ring-emerald-200'
                                : 'bg-plomo/10 text-plomo ring-plomo/20'
                        }`}
                    >
                        {section.is_active ? 'Activa' : 'Inactiva'}
                    </span>
                </div>

                <Card className="mb-6">
                    <h2 className="text-sm font-bold uppercase tracking-wider text-navy-900">
                        Ubicación en la estructura
                    </h2>
                    <dl className="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Nivel
                            </dt>
                            <dd className="mt-1 font-medium text-navy-900">
                                {section.grade?.educational_level?.name ?? '—'}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Grado
                            </dt>
                            <dd className="mt-1 font-medium text-navy-900">
                                {section.grade?.name ?? '—'}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Aulas vinculadas
                            </dt>
                            <dd className="mt-1 text-lg font-bold text-navy-900">
                                {section.classrooms_count ?? classrooms.length}
                            </dd>
                        </div>
                    </dl>
                </Card>

                <TableContainer title="Aulas" description="Espacios asociados.">
                    {classrooms.length === 0 ? (
                        <p className="p-6 text-sm text-plomo">
                            No hay aulas vinculadas a esta sección.
                        </p>
                    ) : (
                        <table className="min-w-full text-left text-sm">
                            <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase tracking-wide text-plomo">
                                <tr>
                                    <th className="px-4 py-3 sm:px-6">Código</th>
                                    <th className="px-4 py-3 sm:px-6">Nombre</th>
                                    <th className="px-4 py-3 sm:px-6">Piso</th>
                                    <th className="px-4 py-3 sm:px-6">
                                        Capacidad
                                    </th>
                                    <th className="px-4 py-3 sm:px-6">Estado</th>
                                    <th className="px-4 py-3 text-right sm:px-6">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-plomo/10">
                                {classrooms.map((c) => (
                                    <tr
                                        key={c.id}
                                        className="bg-white hover:bg-navy-50/40"
                                    >
                                        <td className="px-4 py-3 font-mono text-xs font-semibold text-navy-900 sm:px-6">
                                            {c.code}
                                        </td>
                                        <td className="px-4 py-3 font-medium text-navy-900 sm:px-6">
                                            {c.name}
                                        </td>
                                        <td className="px-4 py-3 text-plomo sm:px-6">
                                            {c.floor ?? '—'}
                                        </td>
                                        <td className="px-4 py-3 text-navy-900 sm:px-6">
                                            {c.capacity}
                                        </td>
                                        <td className="px-4 py-3 sm:px-6">
                                            <span
                                                className={`inline-flex rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ${
                                                    c.is_active
                                                        ? 'bg-emerald-50 text-emerald-900 ring-emerald-200'
                                                        : 'bg-plomo/10 text-plomo ring-plomo/20'
                                                }`}
                                            >
                                                {c.is_active ? 'Activa' : 'Inactiva'}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-right sm:px-6">
                                            <Link
                                                href={route(
                                                    'intranet.academic.classrooms.show',
                                                    c.id,
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
