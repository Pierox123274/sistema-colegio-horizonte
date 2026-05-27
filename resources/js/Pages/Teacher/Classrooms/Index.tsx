import { AppBadge } from '@/Components/App/AppBadge';
import { AppCard } from '@/Components/App/AppCard';
import { AppEmptyState } from '@/Components/App/AppEmptyState';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { BookOpenText } from 'lucide-react';

type Row = {
    id: number;
    title: string;
    subject: string | null;
    section: string | null;
    academic_year: string | null;
    assignments_count: number;
    exams_count: number;
};

type Props = PageProps<{
    classrooms: { data: Row[] };
    can_create: boolean;
}>;

export default function TeacherClassroomsIndex() {
    const { classrooms, can_create } = usePage<Props>().props;

    return (
        <TeacherLayout title="Aula virtual">
            <Head title="Aula virtual" />
            <PageContainer>
                <AppPageHeader
                    title="Aulas virtuales"
                    description="Publicaciones, tareas, evaluaciones y recursos por curso y sección."
                    actions={
                        can_create ? (
                            <Link
                                href={route('teacher.classrooms.create')}
                                className="inline-flex items-center rounded-lg bg-brand-navy px-4 py-2.5 text-sm font-semibold text-white app-transition hover:bg-navy-800"
                            >
                                Nueva aula
                            </Link>
                        ) : null
                    }
                />
                <div className="grid gap-4 md:grid-cols-2">
                    {classrooms.data.length === 0 ? (
                        <AppEmptyState
                            icon={BookOpenText}
                            title="No hay aulas registradas"
                            description="Cuando se creen aulas para tus asignaciones aparecerán aquí."
                        />
                    ) : (
                        classrooms.data.map((c) => (
                            <AppCard key={c.id} className="border-l-4 border-l-sky-500">
                                <h2 className="font-bold text-navy-900">{c.title}</h2>
                                <p className="mt-1 text-sm text-plomo">
                                    {c.subject} — {c.section} ({c.academic_year})
                                </p>
                                <p className="mt-2 text-xs text-plomo">
                                    {c.assignments_count} tareas · {c.exams_count} evaluaciones
                                </p>
                                <div className="mt-2">
                                    <AppBadge tone="info">Aula activa</AppBadge>
                                </div>
                                <Link
                                    href={route('teacher.classrooms.show', c.id)}
                                    className="mt-3 inline-block text-sm font-medium text-brand-navy hover:underline"
                                >
                                    Entrar al aula →
                                </Link>
                            </AppCard>
                        ))
                    )}
                </div>
            </PageContainer>
        </TeacherLayout>
    );
}
