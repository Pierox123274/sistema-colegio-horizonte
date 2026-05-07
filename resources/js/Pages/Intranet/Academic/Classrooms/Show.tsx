import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type ClassroomDetail = {
    id: number;
    code: string;
    name: string;
    floor: string | null;
    capacity: number;
    description: string | null;
    is_active: boolean;
    section?: {
        id: number;
        code: string;
        name: string;
        grade?: {
            id: number;
            name: string;
            educational_level?: { id: number; code: string; name: string };
        };
    } | null;
};

type ShowPageProps = PageProps<{
    classroom: ClassroomDetail;
    permissions: { manage: boolean };
}>;

export default function ClassroomsShow() {
    const { classroom, permissions, flash } = usePage<ShowPageProps>().props;

    const sec = classroom.section;

    return (
        <IntranetLayout title={classroom.name}>
            <Head title={`${classroom.name} — Aula — Horizonte`} />

            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        {
                            label: 'Aulas',
                            href: route('intranet.academic.classrooms.index'),
                        },
                        { label: classroom.name },
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
                    title={classroom.name}
                    description={`Código ${classroom.code}`}
                    actions={
                        <div className="flex flex-wrap gap-2">
                            {permissions.manage ? (
                                <Link
                                    href={route(
                                        'intranet.academic.classrooms.edit',
                                        classroom.id,
                                    )}
                                    className="inline-flex items-center rounded-lg border border-plomo/20 bg-white px-4 py-2 text-sm font-semibold text-navy-900 shadow-sm transition hover:bg-navy-50"
                                >
                                    Editar aula
                                </Link>
                            ) : null}
                            {sec ? (
                                <Link
                                    href={route(
                                        'intranet.academic.sections.show',
                                        sec.id,
                                    )}
                                    className="inline-flex items-center rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                                >
                                    Ver sección
                                </Link>
                            ) : null}
                        </div>
                    }
                />

                <div className="mb-6 flex flex-wrap gap-3">
                    <span className="inline-flex rounded-full bg-navy-900/5 px-3 py-1 font-mono text-xs font-semibold text-navy-900 ring-1 ring-navy-900/10">
                        Cupo: {classroom.capacity}
                    </span>
                    {classroom.floor ? (
                        <span className="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-navy-900 ring-1 ring-plomo/20">
                            Piso {classroom.floor}
                        </span>
                    ) : null}
                    <span
                        className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 ${
                            classroom.is_active
                                ? 'bg-emerald-50 text-emerald-900 ring-emerald-200'
                                : 'bg-plomo/10 text-plomo ring-plomo/20'
                        }`}
                    >
                        {classroom.is_active ? 'Activa' : 'Inactiva'}
                    </span>
                </div>

                <Card>
                    <h2 className="text-sm font-bold uppercase tracking-wider text-navy-900">
                        Ubicación académica
                    </h2>
                    {sec ? (
                        <dl className="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <dt className="text-xs font-semibold uppercase text-plomo">
                                    Nivel
                                </dt>
                                <dd className="mt-1 font-medium text-navy-900">
                                    {sec.grade?.educational_level?.name ?? '—'}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-xs font-semibold uppercase text-plomo">
                                    Grado
                                </dt>
                                <dd className="mt-1 font-medium text-navy-900">
                                    {sec.grade?.name ?? '—'}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-xs font-semibold uppercase text-plomo">
                                    Sección
                                </dt>
                                <dd className="mt-1 font-medium text-navy-900">
                                    {sec.name} ({sec.code})
                                </dd>
                            </div>
                        </dl>
                    ) : (
                        <p className="mt-4 text-sm text-plomo">
                            Esta aula no está asignada a ninguna sección.
                        </p>
                    )}
                    {classroom.description ? (
                        <div className="mt-6 border-t border-plomo/10 pt-6">
                            <h3 className="text-xs font-semibold uppercase text-plomo">
                                Descripción
                            </h3>
                            <p className="mt-2 whitespace-pre-wrap text-sm text-navy-900">
                                {classroom.description}
                            </p>
                        </div>
                    ) : null}
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
