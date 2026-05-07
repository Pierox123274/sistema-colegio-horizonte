import { EnrollmentFormFields } from '@/Components/Intranet/EnrollmentFormFields';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import PrimaryButton from '@/Components/PrimaryButton';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type {
    EnrollmentFormCatalog,
    EnrollmentFormState,
    EnrollmentStudentPreview,
    PageProps,
} from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import type { FormEventHandler } from 'react';

type EnrollmentRow = {
    id: number;
    enrollment_code: string;
    student_id: number;
    guardian_id: number | null;
    academic_year_id: number;
    educational_level_id: number;
    grade_id: number;
    section_id: number;
    classroom_id: number | null;
    enrollment_date: string;
    amount: string;
    status: string | { value: string };
    observations: string | null;
};

type EditPageProps = PageProps<{
    enrollment: EnrollmentRow;
    catalog: EnrollmentFormCatalog;
    student_preview: EnrollmentStudentPreview | null;
}>;

function toFormState(enrollment: EnrollmentRow): EnrollmentFormState {
    const status =
        typeof enrollment.status === 'object' && enrollment.status !== null
            ? enrollment.status.value
            : String(enrollment.status);

    const date =
        typeof enrollment.enrollment_date === 'string'
            ? enrollment.enrollment_date.slice(0, 10)
            : '';

    return {
        enrollment_code: enrollment.enrollment_code,
        student_id: String(enrollment.student_id),
        guardian_id: enrollment.guardian_id
            ? String(enrollment.guardian_id)
            : '',
        academic_year_id: String(enrollment.academic_year_id),
        educational_level_id: String(enrollment.educational_level_id),
        grade_id: String(enrollment.grade_id),
        section_id: String(enrollment.section_id),
        classroom_id: enrollment.classroom_id
            ? String(enrollment.classroom_id)
            : '',
        enrollment_date: date,
        amount: String(enrollment.amount),
        status,
        observations: enrollment.observations ?? '',
    };
}

export default function EnrollmentsEdit() {
    const { enrollment, catalog, student_preview } =
        usePage<EditPageProps>().props;

    const form = useForm<EnrollmentFormState>(toFormState(enrollment));

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.put(
            route('intranet.enrollments.update', enrollment.id),
        );
    };

    return (
        <IntranetLayout title="Editar matrícula">
            <Head title={`Matrícula ${enrollment.enrollment_code} — Horizonte`} />

            <PageContainer>
                <SectionTitle
                    title="Editar matrícula"
                    description={`Código ${enrollment.enrollment_code}`}
                    actions={
                        <div className="flex flex-wrap gap-3">
                            <Link
                                href={route(
                                    'intranet.enrollments.show',
                                    enrollment.id,
                                )}
                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Ver detalle
                            </Link>
                            <Link
                                href={route('intranet.enrollments.index')}
                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Volver al listado
                            </Link>
                        </div>
                    }
                />

                <Card>
                    <form onSubmit={submit} className="space-y-8">
                        <EnrollmentFormFields
                            form={form}
                            catalog={catalog}
                            initialStudentPreview={student_preview}
                        />

                        <div className="flex flex-wrap items-center gap-3 border-t border-plomo/10 pt-6">
                            <PrimaryButton disabled={form.processing}>
                                Guardar cambios
                            </PrimaryButton>
                            <Link
                                href={route(
                                    'intranet.enrollments.show',
                                    enrollment.id,
                                )}
                                className="text-sm font-medium text-plomo hover:text-navy-900"
                            >
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
