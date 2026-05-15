import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function SubjectCreate() {
    const form = useForm({
        code: '',
        name: '',
        description: '',
        is_active: true,
    });

    return (
        <IntranetLayout title="Nuevo curso">
            <Head title="Nuevo curso" />
            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Gestión académica' }, { label: 'Cursos' }, { label: 'Nuevo' }]} />
                <SectionTitle
                    title="Crear curso"
                    description="Registrar asignatura para evaluaciones y notas."
                    actions={<Link href={route('intranet.academic.subjects.index')} className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold">Volver</Link>}
                />
                <Card>
                    <form
                        className="grid gap-4 md:grid-cols-2"
                        onSubmit={(e) => {
                            e.preventDefault();
                            form.post(route('intranet.academic.subjects.store'));
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
                            <button type="submit" disabled={form.processing} className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Guardar curso</button>
                        </div>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}

