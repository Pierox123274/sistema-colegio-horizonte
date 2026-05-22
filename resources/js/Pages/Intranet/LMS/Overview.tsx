import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type Props = PageProps<{
    overview: { classrooms: number; assignments: number; submissions: number; exam_attempts: number };
}>;

export default function IntranetLMSOverview() {
    const { overview } = usePage<Props>().props;

    return (
        <IntranetLayout title="LMS institucional">
            <Head title="Aula virtual — institución" />
            <PageContainer>
                <SectionTitle title="Uso de la plataforma LMS" description="Agregados de aulas, tareas y evaluaciones." />
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <p className="text-xs uppercase text-plomo">Aulas activas</p>
                        <p className="text-2xl font-bold">{overview.classrooms}</p>
                    </Card>
                    <Card>
                        <p className="text-xs uppercase text-plomo">Tareas</p>
                        <p className="text-2xl font-bold">{overview.assignments}</p>
                    </Card>
                    <Card>
                        <p className="text-xs uppercase text-plomo">Entregas</p>
                        <p className="text-2xl font-bold">{overview.submissions}</p>
                    </Card>
                    <Card>
                        <p className="text-xs uppercase text-plomo">Intentos examen</p>
                        <p className="text-2xl font-bold">{overview.exam_attempts}</p>
                    </Card>
                </div>
            </PageContainer>
        </IntranetLayout>
    );
}
