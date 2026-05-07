import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { RELATIONSHIP_LABELS } from '@/lib/guardianLabels';
import { DOCUMENT_TYPE_LABELS } from '@/lib/studentLabels';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type {
    GuardianSerializable,
    GuardianStudentLinkView,
    PageProps,
} from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Pencil } from 'lucide-react';

type ShowPageProps = PageProps<{
    guardian: GuardianSerializable;
    student_links: GuardianStudentLinkView[];
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

export default function GuardiansShow() {
    const { guardian, student_links, permissions, flash } =
        usePage<ShowPageProps>().props;

    return (
        <IntranetLayout title="Detalle apoderado">
            <Head
                title={`${guardian.first_name} ${guardian.last_name} — Horizonte`}
            />

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
                    title={`${guardian.first_name} ${guardian.last_name}`}
                    description="Ficha de apoderado y estudiantes vinculados."
                    actions={
                        <div className="flex flex-wrap items-center gap-3">
                            <Link
                                href={route('intranet.guardians.index')}
                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Listado
                            </Link>
                            {permissions.manage ? (
                                <Link
                                    href={route(
                                        'intranet.guardians.edit',
                                        guardian.id,
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
                            <span className="inline-flex rounded-full bg-navy-900/5 px-2.5 py-1 text-xs font-semibold text-navy-900 ring-1 ring-navy-900/10">
                                {RELATIONSHIP_LABELS[guardian.relationship_type] ??
                                    guardian.relationship_type}
                            </span>
                            {guardian.is_emergency_contact ? (
                                <span className="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-900 ring-1 ring-amber-200">
                                    Emergencia
                                </span>
                            ) : null}
                        </div>
                        <p className="mt-4 text-sm text-plomo">
                            {student_links.length} estudiante(s) vinculado(s).
                        </p>
                    </Card>

                    <Card className="lg:col-span-2">
                        <h3 className="mb-2 text-xs font-bold uppercase tracking-wider text-plomo">
                            Identificación
                        </h3>
                        <dl>
                            <DetailRow
                                label="Tipo de documento"
                                value={
                                    DOCUMENT_TYPE_LABELS[guardian.document_type] ??
                                    guardian.document_type
                                }
                            />
                            <DetailRow
                                label="Número"
                                value={guardian.document_number}
                            />
                        </dl>

                        <h3 className="mb-2 mt-6 text-xs font-bold uppercase tracking-wider text-plomo">
                            Contacto
                        </h3>
                        <dl>
                            <DetailRow label="Teléfono" value={guardian.phone} />
                            <DetailRow
                                label="Teléfono secundario"
                                value={guardian.secondary_phone}
                            />
                            <DetailRow label="Correo" value={guardian.email} />
                            <DetailRow
                                label="Dirección"
                                value={guardian.address}
                            />
                        </dl>

                        <h3 className="mb-2 mt-6 text-xs font-bold uppercase tracking-wider text-plomo">
                            Laboral
                        </h3>
                        <dl>
                            <DetailRow
                                label="Ocupación"
                                value={guardian.occupation}
                            />
                            <DetailRow
                                label="Centro de trabajo"
                                value={guardian.workplace}
                            />
                        </dl>
                    </Card>
                </div>

                <Card className="mt-6">
                    <h3 className="mb-4 text-sm font-bold uppercase tracking-wider text-navy-900">
                        Estudiantes vinculados
                    </h3>
                    {student_links.length === 0 ? (
                        <p className="text-sm text-plomo">
                            No hay estudiantes asociados a esta ficha.
                        </p>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="min-w-full text-left text-sm">
                                <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo">
                                    <tr>
                                        <th className="px-3 py-2">Estudiante</th>
                                        <th className="px-3 py-2">Parentesco</th>
                                        <th className="px-3 py-2">Principal</th>
                                        <th className="px-3 py-2">Económico</th>
                                        <th className="px-3 py-2">Prioridad</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-plomo/10">
                                    {student_links.map((row) => (
                                        <tr key={row.student_id}>
                                            <td className="px-3 py-2">
                                                <div className="font-medium text-navy-900">
                                                    {row.student_name}
                                                </div>
                                                <div className="font-mono text-xs text-plomo">
                                                    {row.student_code}
                                                </div>
                                            </td>
                                            <td className="px-3 py-2">
                                                {RELATIONSHIP_LABELS[row.relationship] ??
                                                    row.relationship}
                                            </td>
                                            <td className="px-3 py-2">
                                                {row.is_primary ? 'Sí' : '—'}
                                            </td>
                                            <td className="px-3 py-2">
                                                {row.is_financial_responsible
                                                    ? 'Sí'
                                                    : '—'}
                                            </td>
                                            <td className="px-3 py-2">
                                                {row.emergency_priority ?? '—'}
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
