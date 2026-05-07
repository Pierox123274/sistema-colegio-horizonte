import { Card } from '@/Components/Intranet/Card';
import { GuardianFormFields } from '@/Components/Intranet/GuardianFormFields';
import {
    GuardianFullFormState,
    GuardianStudentLinksEditor,
} from '@/Components/Intranet/GuardianStudentLinksEditor';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import PrimaryButton from '@/Components/PrimaryButton';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type {
    GuardianFormCatalog,
    PageProps,
    StudentOption,
} from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';

type CreatePageProps = PageProps<{
    catalog: GuardianFormCatalog;
    student_options: StudentOption[];
}>;

export default function GuardiansCreate() {
    const { catalog, student_options } = usePage<CreatePageProps>().props;

    const initial: GuardianFullFormState = {
        first_name: '',
        last_name: '',
        document_type: catalog.document_types[0]?.value ?? 'dni',
        document_number: '',
        relationship_type:
            catalog.relationship_types[0]?.value ?? 'padre',
        phone: '',
        secondary_phone: '',
        email: '',
        occupation: '',
        address: '',
        workplace: '',
        is_emergency_contact: false,
        students: [],
    };

    const form = useForm(initial);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.post(route('intranet.guardians.store'));
    };

    return (
        <IntranetLayout title="Nuevo apoderado">
            <Head title="Nuevo apoderado — Horizonte" />

            <PageContainer>
                <SectionTitle
                    title="Registrar apoderado"
                    description="Datos personales y vínculos con estudiantes existentes."
                    actions={
                        <Link
                            href={route('intranet.guardians.index')}
                            className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                        >
                            Volver al listado
                        </Link>
                    }
                />

                <Card>
                    <form onSubmit={submit} className="space-y-10">
                        <GuardianFormFields form={form} catalog={catalog} />

                        <GuardianStudentLinksEditor
                            form={form}
                            catalog={catalog}
                            studentOptions={student_options}
                        />

                        <div className="flex flex-wrap items-center gap-3 border-t border-plomo/10 pt-6">
                            <PrimaryButton disabled={form.processing}>
                                Guardar apoderado
                            </PrimaryButton>
                            <Link
                                href={route('intranet.guardians.index')}
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
