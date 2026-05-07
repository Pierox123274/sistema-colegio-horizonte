import { EnrollmentFormFields } from '@/Components/Intranet/EnrollmentFormFields';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import PrimaryButton from '@/Components/PrimaryButton';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type {
    EnrollmentFormCatalog,
    EnrollmentFormState,
    PageProps,
} from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import type { FormEventHandler } from 'react';

type CreatePageProps = PageProps<{
    catalog: EnrollmentFormCatalog;
}>;

function initialFromCatalog(catalog: EnrollmentFormCatalog): EnrollmentFormState {
    const firstLevel = catalog.levels[0]?.value ?? '';
    const firstGrade =
        (catalog.grades_by_level[firstLevel] ?? [])[0]?.value ?? '';
    const firstSection =
        (catalog.sections_by_grade[firstGrade] ?? [])[0]?.value ?? '';

    return {
        enrollment_code: '',
        student_id: '',
        guardian_id: '',
        academic_year_id: catalog.academic_years[0]?.value ?? '',
        educational_level_id: firstLevel,
        grade_id: firstGrade,
        section_id: firstSection,
        classroom_id: '',
        enrollment_date: new Date().toISOString().slice(0, 10),
        amount: '0',
        status: 'pendiente',
        observations: '',
    };
}

export default function EnrollmentsCreate() {
    const { catalog } = usePage<CreatePageProps>().props;

    const form = useForm<EnrollmentFormState>(initialFromCatalog(catalog));

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.post(route('intranet.enrollments.store'));
    };

    return (
        <IntranetLayout title="Nueva matrícula">
            <Head title="Nueva matrícula — Horizonte" />

            <PageContainer>
                <SectionTitle
                    title="Registrar matrícula"
                    description="Vincule al estudiante con el año académico y la ubicación en la malla curricular."
                    actions={
                        <div className="flex flex-wrap gap-3">
                            <Link
                                href={route('intranet.academic-years.index')}
                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Años académicos
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
                            enrollmentCodeOptional
                        />

                        <div className="flex flex-wrap items-center gap-3 border-t border-plomo/10 pt-6">
                            <PrimaryButton disabled={form.processing}>
                                Guardar matrícula
                            </PrimaryButton>
                            <Link
                                href={route('intranet.enrollments.index')}
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
