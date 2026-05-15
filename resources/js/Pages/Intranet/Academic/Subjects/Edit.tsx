import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';

type Props = PageProps<{ subject: { id: number; code: string; name: string; description: string | null; is_active: boolean } }>;

export default function SubjectEdit() {
    const { subject } = usePage<Props>().props;
    const form = useForm({
        code: subject.code,
        name: subject.name,
        description: subject.description ?? '',
        is_active: subject.is_active,
    });

    return (
        <IntranetLayout title="Editar curso">
            <Head title="Editar curso" />
            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Gestión académica' }, { label: 'Cursos' }, { label: 'Editar' }]} />
                <SectionTitle
                    title="Editar curso"
                    description="Actualiza información del curso."
                    actions={<Link href={route('intranet.academic.subjects.index')} className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold">Volver</Link>}
                />
                <Card>
                    <form
                        className="grid gap-4 md:grid-cols-2"
                        onSubmit={(e) => {
                            e.preventDefault();
                            form.put(route('intranet.academic.subjects.update', subject.id));
                        }}
                    >
                        <div>
                            <label className="text-xs font-semibold uppercase text-plomo">Código</label>
                            <input value={form.data.code} onChange={(e) => form.setData('code', e.target.value)} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label className="text-xs font-semibold uppercase text-plomo">Nombre</label>
                            <input value={form.data.name} onChange={(e) => form.setData('name', e.target.value)} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm" />
                        </div>
                        <div className="md:col-span-2">
                            <label className="text-xs font-semibold uppercase text-plomo">Descripción</label>
                            <textarea value={form.data.description} onChange={(e) => form.setData('description', e.target.value)} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm" rows={3} />
                        </div>
                        <div className="md:col-span-2">
                            <button type="submit" disabled={form.processing} className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Guardar cambios</button>
                        </div>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}

