import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { RELATIONSHIP_LABELS } from '@/lib/guardianLabels';
import {
    DOCUMENT_TYPE_LABELS,
    EDUCATIONAL_LEVEL_LABELS,
    GENDER_LABELS,
    STATUS_LABELS,
    levelBadgeClass,
    statusBadgeClass,
} from '@/lib/studentLabels';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type {
    PageProps,
    StudentGuardianLinkView,
    StudentSerializable,
} from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Pencil } from 'lucide-react';

type ShowPageProps = PageProps<{
    student: StudentSerializable;
    guardian_links: StudentGuardianLinkView[];
    permissions: {
        manage: boolean;
    };
}>;

function DetailRow({
    label,
    value,
}: {
    label: string;
    value: string | null | undefined;
}) {
    return (
        <div className="border-b border-plomo/10 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt className="text-xs font-semibold uppercase tracking-wide text-plomo">
                {label}
            </dt>
            <dd className="mt-1 text-sm text-navy-900 sm:col-span-2 sm:mt-0">
                {value && String(value).trim() !== '' ? value : '—'}
            </dd>
        </div>
    );
}

export default function StudentsShow() {
    const { student, guardian_links, permissions, flash } =
        usePage<ShowPageProps>().props;

    const birth =
        student.birth_date && student.birth_date.length >= 10
            ? student.birth_date.slice(0, 10)
            : student.birth_date;

    return (
        <IntranetLayout title="Detalle estudiante">
            <Head title={`${student.first_name} ${student.last_name} — Horizonte`} />

            <PageContainer>
                {flash?.success ? (
                    <div
                        className="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900"
                        role="status"
                    >
                        {flash.success}
                    </div>
                ) : null}

                <SectionTitle
                    title={`${student.first_name} ${student.last_name}`}
                    description={`Código ${student.code}`}
                    actions={
                        <div className="flex flex-wrap items-center gap-3">
                            <Link
                                href={route('intranet.students.index')}
                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Listado
                            </Link>
                            {permissions.manage ? (
                                <Link
                                    href={route(
                                        'intranet.students.edit',
                                        student.id,
                                    )}
                                    className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                                >
                                    <Pencil className="h-4 w-4" aria-hidden />
                                    Editar
                                </Link>
                            ) : null}
                        </div>
                    }
                />

                <div className="grid gap-6 lg:grid-cols-3">
                    <Card className="lg:col-span-1">
                        <h3 className="text-xs font-bold uppercase tracking-wider text-plomo">
                            Resumen
                        </h3>
                        <div className="mt-4 flex flex-wrap gap-2">
                            <span
                                className={`inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset ${levelBadgeClass(student.educational_level)}`}
                            >
                                {EDUCATIONAL_LEVEL_LABELS[
                                    student.educational_level
                                ] ?? student.educational_level}
                            </span>
                            <span
                                className={`inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset ${statusBadgeClass(student.status)}`}
                            >
                                {STATUS_LABELS[student.status] ??
                                    student.status}
                            </span>
                        </div>
                        <p className="mt-4 font-mono text-sm text-navy-900">
                            {student.grade}
                            {student.section ? ` · Sección ${student.section}` : ''}
                        </p>
                    </Card>

                    <Card className="lg:col-span-2">
                        <h3 className="mb-2 text-xs font-bold uppercase tracking-wider text-plomo">
                            Datos personales
                        </h3>
                        <dl>
                            <DetailRow
                                label="Tipo de documento"
                                value={
                                    DOCUMENT_TYPE_LABELS[
                                        student.document_type
                                    ] ?? student.document_type
                                }
                            />
                            <DetailRow
                                label="Número de documento"
                                value={student.document_number}
                            />
                            <DetailRow label="Fecha de nacimiento" value={birth} />
                            <DetailRow
                                label="Género"
                                value={
                                    GENDER_LABELS[student.gender] ??
                                    student.gender
                                }
                            />
                        </dl>

                        <h3 className="mb-2 mt-6 text-xs font-bold uppercase tracking-wider text-plomo">
                            Contacto
                        </h3>
                        <dl>
                            <DetailRow label="Dirección" value={student.address} />
                            <DetailRow label="Teléfono" value={student.phone} />
                            <DetailRow label="Correo" value={student.email} />
                        </dl>

                        <h3 className="mb-2 mt-6 text-xs font-bold uppercase tracking-wider text-plomo">
                            Salud
                        </h3>
                        <p className="rounded-lg bg-navy-50/80 px-3 py-2 text-sm text-navy-900">
                            {student.medical_observations ?? 'Sin observaciones registradas.'}
                        </p>
                    </Card>
                </div>

                <Card className="mt-6">
                    <h3 className="mb-4 text-sm font-bold uppercase tracking-wider text-navy-900">
                        Apoderados vinculados
                    </h3>
                    {guardian_links.length === 0 ? (
                        <p className="text-sm text-plomo">
                            No hay apoderados asociados a este estudiante.
                            Los vínculos se gestionan desde el módulo de apoderados.
                        </p>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="min-w-full text-left text-sm">
                                <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo">
                                    <tr>
                                        <th className="px-3 py-2">
                                            Apoderado
                                        </th>
                                        <th className="px-3 py-2">
                                            Parentesco
                                        </th>
                                        <th className="px-3 py-2">
                                            Teléfono
                                        </th>
                                        <th className="hidden px-3 py-2 md:table-cell">
                                            Documento
                                        </th>
                                        <th className="hidden px-3 py-2 lg:table-cell">
                                            Correo
                                        </th>
                                        <th className="px-3 py-2 text-center">
                                            Principal
                                        </th>
                                        <th className="px-3 py-2 text-center">
                                            Económico
                                        </th>
                                        <th className="px-3 py-2 text-center">
                                            Urg.
                                        </th>
                                        <th className="hidden px-3 py-2 xl:table-cell">
                                            Observaciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-plomo/10">
                                    {guardian_links.map((g) => (
                                        <tr key={g.id}>
                                            <td className="px-3 py-2 font-medium text-navy-900">
                                                {g.full_name}
                                            </td>
                                            <td className="px-3 py-2">
                                                {RELATIONSHIP_LABELS[g.relationship] ??
                                                    g.relationship}
                                            </td>
                                            <td className="px-3 py-2 text-navy-900">
                                                {g.phone}
                                            </td>
                                            <td className="hidden px-3 py-2 font-mono text-xs text-plomo md:table-cell">
                                                {g.document_number ?? '—'}
                                            </td>
                                            <td className="hidden px-3 py-2 text-plomo lg:table-cell">
                                                {g.email ?? '—'}
                                            </td>
                                            <td className="px-3 py-2 text-center">
                                                {g.is_primary ? (
                                                    <span className="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-900 ring-1 ring-emerald-200">
                                                        Sí
                                                    </span>
                                                ) : (
                                                    '—'
                                                )}
                                            </td>
                                            <td className="px-3 py-2 text-center">
                                                {g.is_financial_responsible ? (
                                                    <span className="inline-flex rounded-full bg-navy-900/10 px-2 py-0.5 text-xs font-semibold text-navy-900 ring-1 ring-navy-900/15">
                                                        Sí
                                                    </span>
                                                ) : (
                                                    '—'
                                                )}
                                            </td>
                                            <td className="px-3 py-2 text-center text-navy-900">
                                                {g.emergency_priority ?? '—'}
                                            </td>
                                            <td className="hidden max-w-xs px-3 py-2 text-plomo xl:table-cell">
                                                {g.observations ?? '—'}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
