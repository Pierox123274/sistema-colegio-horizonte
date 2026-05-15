import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type Props = PageProps<{ evaluation: any }>;

export default function EvaluationShow() {
    const { evaluation } = usePage<Props>().props;

    return (
        <IntranetLayout title="Detalle de evaluación">
            <Head title="Detalle de evaluación" />
            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Gestión académica' }, { label: 'Evaluaciones' }, { label: 'Detalle' }]} />
                <SectionTitle title="Detalle de evaluación" description="Datos de la evaluación seleccionada." actions={<Link href={route('intranet.academic.evaluations.index')} className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold">Volver</Link>} />
                <Card>
                    <div className="space-y-2 text-sm">
                        <p><span className="font-semibold">Curso:</span> {evaluation.subject?.name}</p>
                        <p><span className="font-semibold">Título:</span> {evaluation.title}</p>
                        <p><span className="font-semibold">Periodo:</span> {evaluation.period}</p>
                        <p><span className="font-semibold">Fecha:</span> {evaluation.evaluated_at}</p>
                        <p><span className="font-semibold">Nivel/Grado/Sección:</span> {evaluation.educational_level?.name} / {evaluation.grade?.name} / {evaluation.section?.name}</p>
                        <p><span className="font-semibold">Nota máxima:</span> {evaluation.max_score}</p>
                    </div>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}

