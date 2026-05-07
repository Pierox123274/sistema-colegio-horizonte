import { Card } from '@/Components/Intranet/Card';
import { GuardianFormFields } from '@/Components/Intranet/GuardianFormFields';
import {
    GuardianFullFormState,
    GuardianStudentLinkDraft,
    GuardianStudentLinksEditor,
} from '@/Components/Intranet/GuardianStudentLinksEditor';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import PrimaryButton from '@/Components/PrimaryButton';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type {
    GuardianFormCatalog,
    GuardianSerializable,
    GuardianStudentLinkView,
    PageProps,
    StudentOption,
} from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler, useMemo } from 'react';

type EditPageProps = PageProps<{
    guardian: GuardianSerializable;
    student_links: GuardianStudentLinkView[];
    catalog: GuardianFormCatalog;
    student_options: StudentOption[];
}>;

function mapLinksToDraft(
    links: GuardianStudentLinkView[],
): GuardianStudentLinkDraft[] {
    return links.map((l) => ({
        student_id: l.student_id,
        relationship: l.relationship,
        is_primary: l.is_primary,
        is_financial_responsible: l.is_financial_responsible,
        emergency_priority:
            l.emergency_priority === null || l.emergency_priority === undefined
                ? ''
                : l.emergency_priority,
        observations: l.observations ?? '',
    }));
}

export default function GuardiansEdit() {
    const { guardian, student_links, catalog, student_options } =
        usePage<EditPageProps>().props;

    const defaults = useMemo((): GuardianFullFormState => {
        return {
            first_name: guardian.first_name,
            last_name: guardian.last_name,
            document_type: guardian.document_type,
            document_number: guardian.document_number ?? '',
            relationship_type: guardian.relationship_type,
            phone: guardian.phone,
            secondary_phone: guardian.secondary_phone ?? '',
            email: guardian.email ?? '',
            occupation: guardian.occupation ?? '',
            address: guardian.address ?? '',
            workplace: guardian.workplace ?? '',
            is_emergency_contact: guardian.is_emergency_contact,
            students: mapLinksToDraft(student_links),
        };
    }, [guardian, student_links]);

    const form = useForm(defaults);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.put(route('intranet.guardians.update', guardian.id));
    };

    return (
        <IntranetLayout title="Editar apoderado">
            <Head title={`Editar ${guardian.first_name} — Horizonte`} />

            <PageContainer>
                <SectionTitle
                    title="Editar apoderado"
                    description={`${guardian.first_name} ${guardian.last_name}`}
                    actions={
                        <Link
                            href={route(
                                'intranet.guardians.show',
                                guardian.id,
                            )}
                            className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                        >
                            Ver detalle
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
                                Guardar cambios
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
