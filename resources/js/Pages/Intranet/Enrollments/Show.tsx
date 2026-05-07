import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Pencil } from 'lucide-react';

const STATUS_BADGE: Record<string, string> = {
    pendiente: 'bg-amber-50 text-amber-900 ring-amber-200',
    matriculado: 'bg-emerald-50 text-emerald-900 ring-emerald-200',
    anulado: 'bg-plomo/15 text-plomo ring-plomo/25',
    retirado: 'bg-rose-50 text-rose-900 ring-rose-200',
};

type Rel = {
    id: number;
    code?: string;
    name?: string;
    first_name?: string;
    last_name?: string;
    year?: number;
};

type EnrollmentDetail = {
    id: number;
    enrollment_code: string;
    enrollment_date: string;
    amount: string;
    status: string | { value: string };
    observations: string | null;
    student?: Rel & { code: string; first_name: string; last_name: string };
    guardian?: Rel | null;
    academicYear?: { id: number; name: string; year: number };
    educationalLevel?: { id: number; code: string; name: string };
    grade?: { id: number; code: string; name: string };
    section?: { id: number; code: string; name: string };
    classroom?: { id: number; code: string; name: string } | null;
};

type ShowPageProps = PageProps<{
    enrollment: EnrollmentDetail;
    permissions: { manage: boolean };
    statusLabels?: SelectOption[];
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

function statusValue(s: EnrollmentDetail['status']): string {
    if (typeof s === 'object' && s !== null) {
        return s.value;
    }
    return String(s);
}

export default function EnrollmentsShow() {
    const { enrollment, permissions, flash } = usePage<ShowPageProps>().props;

    const st = statusValue(enrollment.status);
    const date =
        enrollment.enrollment_date &&
        enrollment.enrollment_date.length >= 10
            ? enrollment.enrollment_date.slice(0, 10)
            : enrollment.enrollment_date;

    return (
        <IntranetLayout title="Detalle matrícula">
            <Head
                title={`${enrollment.enrollment_code} — Matrícula — Horizonte`}
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
                    title={enrollment.enrollment_code}
                    description={`Registro institucional · ${date}`}
                    actions={
                        <div className="flex flex-wrap items-center gap-3">
                            <Link
                                href={route('intranet.enrollments.index')}
                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Listado
                            </Link>
                            {permissions.manage ? (
                                <Link
                                    href={route(
                                        'intranet.enrollments.edit',
                                        enrollment.id,
                                    )}
                                    className="inline-flex items-center gap-2 rounded-lg border border-plomo/20 bg-white px-3 py-1.5 text-sm font-semibold text-navy-900 shadow-sm hover:bg-navy-50"
                                >
                                    <Pencil className="h-4 w-4" />
                                    Editar
                                </Link>
                            ) : null}
                        </div>
                    }
                />

                <div className="mb-4">
                    <span
                        className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 ${
                            STATUS_BADGE[st] ??
                            'bg-plomo/10 text-plomo ring-plomo/20'
                        }`}
                    >
                        {st}
                    </span>
                </div>

                <Card>
                    <dl>
                        <DetailRow
                            label="Estudiante"
                            value={
                                enrollment.student
                                    ? `${enrollment.student.first_name} ${enrollment.student.last_name} (${enrollment.student.code})`
                                    : undefined
                            }
                        />
                        {enrollment.student ? (
                            <div className="border-b border-plomo/10 py-2 sm:pl-[33.333%]">
                                <Link
                                    href={route(
                                        'intranet.students.show',
                                        enrollment.student.id,
                                    )}
                                    className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                                >
                                    Abrir ficha del estudiante
                                </Link>
                            </div>
                        ) : null}
                        <DetailRow
                            label="Apoderado"
                            value={
                                enrollment.guardian
                                    ? `${enrollment.guardian.first_name ?? ''} ${enrollment.guardian.last_name ?? ''}`.trim()
                                    : undefined
                            }
                        />
                        {enrollment.guardian ? (
                            <div className="border-b border-plomo/10 py-2 sm:pl-[33.333%]">
                                <Link
                                    href={route(
                                        'intranet.guardians.show',
                                        enrollment.guardian.id,
                                    )}
                                    className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                                >
                                    Abrir ficha del apoderado
                                </Link>
                            </div>
                        ) : null}
                        <DetailRow
                            label="Año académico"
                            value={
                                enrollment.academicYear
                                    ? `${enrollment.academicYear.name} (${enrollment.academicYear.year})`
                                    : undefined
                            }
                        />
                        <DetailRow
                            label="Nivel"
                            value={
                                enrollment.educationalLevel
                                    ? `${enrollment.educationalLevel.code} — ${enrollment.educationalLevel.name}`
                                    : undefined
                            }
                        />
                        <DetailRow
                            label="Grado"
                            value={
                                enrollment.grade
                                    ? `${enrollment.grade.code} — ${enrollment.grade.name}`
                                    : undefined
                            }
                        />
                        <DetailRow
                            label="Sección"
                            value={
                                enrollment.section
                                    ? `${enrollment.section.code} — ${enrollment.section.name}`
                                    : undefined
                            }
                        />
                        <DetailRow
                            label="Aula"
                            value={
                                enrollment.classroom
                                    ? `${enrollment.classroom.code} — ${enrollment.classroom.name}`
                                    : undefined
                            }
                        />
                        <DetailRow
                            label="Monto"
                            value={`S/ ${Number(enrollment.amount).toFixed(2)}`}
                        />
                        <div className="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt className="text-xs font-semibold uppercase tracking-wide text-plomo">
                                Observaciones
                            </dt>
                            <dd className="mt-1 whitespace-pre-wrap text-sm text-navy-900 sm:col-span-2 sm:mt-0">
                                {enrollment.observations?.trim()
                                    ? enrollment.observations
                                    : '—'}
                            </dd>
                        </div>
                    </dl>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
