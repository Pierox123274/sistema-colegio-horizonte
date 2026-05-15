import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type Props = PageProps<{ subject: { id: number; code: string; name: string; description: string | null; is_active: boolean } }>;

export default function SubjectShow() {
    const { subject } = usePage<Props>().props;

    return (
        <IntranetLayout title="Detalle de curso">
            <Head title="Detalle de curso" />
            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Gestión académica' }, { label: 'Cursos' }, { label: 'Detalle' }]} />
                <SectionTitle
                    title="Detalle del curso"
                    description="Información general de la asignatura."
                    actions={<Link href={route('intranet.academic.subjects.index')} className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold">Volver</Link>}
                />
                <Card>
                    <div className="space-y-2 text-sm">
                        <p><span className="font-semibold">Código:</span> {subject.code}</p>
                        <p><span className="font-semibold">Nombre:</span> {subject.name}</p>
                        <p><span className="font-semibold">Descripción:</span> {subject.description ?? 'Sin descripción'}</p>
                        <p><span className="font-semibold">Estado:</span> {subject.is_active ? 'Activo' : 'Inactivo'}</p>
                    </div>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}

