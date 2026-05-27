import { AppBadge } from '@/Components/App/AppBadge';
import { AppCard } from '@/Components/App/AppCard';
import { AppEmptyState } from '@/Components/App/AppEmptyState';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { BookMarked } from 'lucide-react';

type Props = PageProps<{
    classrooms: { data: { id: number; title: string; subject: string | null; section: string | null }[] };
}>;

export default function StudentClassroomsIndex() {
    const { classrooms } = usePage<Props>().props;

    return (
        <StudentLayout title="Aula virtual">
            <Head title="Aula virtual" />
            <PageContainer>
                <AppPageHeader
                    title="Mis aulas virtuales"
                    description="Tareas, materiales y evaluaciones de tus cursos."
                />
                <div className="grid gap-4 md:grid-cols-2">
                    {classrooms.data.length === 0 ? (
                        <AppEmptyState
                            icon={BookMarked}
                            title="No tienes aulas asignadas"
                            description="Cuando tu matrícula esté vinculada a cursos verás tus aulas aquí."
                        />
                    ) : (
                        classrooms.data.map((c) => (
                        <AppCard key={c.id}>
                            <h2 className="font-bold text-navy-900">{c.title}</h2>
                            <p className="text-sm text-plomo">
                                {c.subject} — {c.section}
                            </p>
                            <div className="mt-2">
                                <AppBadge tone="info">Curso activo</AppBadge>
                            </div>
                            <Link
                                href={route('student.classrooms.show', c.id)}
                                className="mt-3 inline-block text-sm font-medium text-brand-navy hover:underline"
                            >
                                Entrar →
                            </Link>
                        </AppCard>
                    )))}
                </div>
            </PageContainer>
        </StudentLayout>
    );
}
