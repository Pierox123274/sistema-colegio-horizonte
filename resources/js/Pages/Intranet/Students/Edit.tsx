import { Card } from '@/Components/Intranet/Card';
import {
    StudentFormFields,
    StudentFormState,
} from '@/Components/Intranet/StudentFormFields';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import PrimaryButton from '@/Components/PrimaryButton';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type {
    PageProps,
    StudentFormCatalog,
    StudentSerializable,
} from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler, useMemo } from 'react';

type EditPageProps = PageProps<{
    student: StudentSerializable;
    catalog: StudentFormCatalog;
}>;

function toFormState(s: StudentSerializable): StudentFormState {
    const birth =
        s.birth_date && s.birth_date.length >= 10
            ? s.birth_date.slice(0, 10)
            : '';

    return {
        code: s.code,
        first_name: s.first_name,
        last_name: s.last_name,
        document_type: s.document_type,
        document_number: s.document_number ?? '',
        birth_date: birth,
        gender: s.gender,
        educational_level: s.educational_level,
        grade: s.grade,
        section: s.section ?? '',
        status: s.status,
        address: s.address ?? '',
        phone: s.phone ?? '',
        email: s.email ?? '',
        medical_observations: s.medical_observations ?? '',
    };
}

export default function StudentsEdit() {
    const { student, catalog } = usePage<EditPageProps>().props;

    const defaults = useMemo(() => toFormState(student), [student]);

    const form = useForm(defaults);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.put(route('intranet.students.update', student.id));
    };

    return (
        <IntranetLayout title="Editar estudiante">
            <Head title={`Editar ${student.first_name} — Horizonte`} />

            <PageContainer>
                <SectionTitle
                    title="Editar estudiante"
                    description={`Ficha ${student.code}`}
                    actions={
                        <Link
                            href={route('intranet.students.show', student.id)}
                            className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                        >
                            Ver detalle
                        </Link>
                    }
                />

                <Card>
                    <form onSubmit={submit} className="space-y-8">
                        <StudentFormFields form={form} catalog={catalog} />

                        <div className="flex flex-wrap items-center gap-3 border-t border-plomo/10 pt-6">
                            <PrimaryButton disabled={form.processing}>
                                Guardar cambios
                            </PrimaryButton>
                            <Link
                                href={route(
                                    'intranet.students.index',
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
