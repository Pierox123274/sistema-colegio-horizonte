import { Card } from '@/Components/Intranet/Card';
import {
    StudentFormFields,
    StudentFormState,
} from '@/Components/Intranet/StudentFormFields';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import PrimaryButton from '@/Components/PrimaryButton';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, StudentFormCatalog } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';

type CreatePageProps = PageProps<{
    catalog: StudentFormCatalog;
}>;

const initial: StudentFormState = {
    code: '',
    first_name: '',
    last_name: '',
    document_type: 'dni',
    document_number: '',
    birth_date: '',
    gender: 'masculino',
    educational_level: 'inicial',
    grade: '3 años',
    section: '',
    status: 'activo',
    address: '',
    phone: '',
    email: '',
    medical_observations: '',
};

export default function StudentsCreate() {
    const { catalog } = usePage<CreatePageProps>().props;

    const form = useForm(initial);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.post(route('intranet.students.store'));
    };

    return (
        <IntranetLayout title="Nuevo estudiante">
            <Head title="Nuevo estudiante — Horizonte" />

            <PageContainer>
                <SectionTitle
                    title="Registrar estudiante"
                    description="Complete los datos obligatorios. El código debe ser único en la institución."
                    actions={
                        <Link
                            href={route('intranet.students.index')}
                            className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                        >
                            Volver al listado
                        </Link>
                    }
                />

                <Card>
                    <form onSubmit={submit} className="space-y-8">
                        <StudentFormFields form={form} catalog={catalog} />

                        <div className="flex flex-wrap items-center gap-3 border-t border-plomo/10 pt-6">
                            <PrimaryButton disabled={form.processing}>
                                Guardar estudiante
                            </PrimaryButton>
                            <Link
                                href={route('intranet.students.index')}
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
