import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { AppStatCard } from '@/Components/App/AppStatCard';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { BookOpen, CheckCheck, ClipboardCheck, PenSquare } from 'lucide-react';

type Props = PageProps<{
    overview: { classrooms: number; assignments: number; submissions: number; exam_attempts: number };
}>;

export default function IntranetLMSOverview() {
    const { overview } = usePage<Props>().props;

    return (
        <IntranetLayout title="LMS institucional">
            <Head title="Aula virtual — institución" />
            <PageContainer>
                <AppPageHeader
                    title="Uso de la plataforma LMS"
                    description="Agregados de aulas, tareas y evaluaciones."
                    eyebrow="LMS Institucional"
                />
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <AppStatCard title="Aulas activas" value={String(overview.classrooms)} icon={BookOpen} />
                    <AppStatCard title="Tareas" value={String(overview.assignments)} icon={PenSquare} />
                    <AppStatCard title="Entregas" value={String(overview.submissions)} icon={CheckCheck} />
                    <AppStatCard title="Intentos examen" value={String(overview.exam_attempts)} icon={ClipboardCheck} />
                </div>
            </PageContainer>
        </IntranetLayout>
    );
}
