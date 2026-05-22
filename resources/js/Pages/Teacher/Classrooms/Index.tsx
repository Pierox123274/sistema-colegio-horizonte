import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

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
                <div className="mb-6 flex justify-between gap-4">
                    <SectionTitle
                        title="Aulas virtuales"
                        description="Publicaciones, tareas, evaluaciones y recursos por curso y sección."
                    />
                    {can_create ? (
                        <Link
                            href={route('teacher.classrooms.create')}
                            className="inline-flex items-center rounded-lg bg-brand-navy px-4 py-2.5 text-sm font-semibold text-white hover:bg-navy-800"
                        >
                            Nueva aula
                        </Link>
                    ) : null}
                </div>
                <div className="grid gap-4 md:grid-cols-2">
                    {classrooms.data.length === 0 ? (
                        <Card>
                            <p className="text-sm text-plomo">No hay aulas registradas para sus asignaciones.</p>
                        </Card>
                    ) : (
                        classrooms.data.map((c) => (
                            <Card key={c.id} className="border-l-4 border-l-sky-500">
                                <h2 className="font-bold text-navy-900">{c.title}</h2>
                                <p className="mt-1 text-sm text-plomo">
                                    {c.subject} — {c.section} ({c.academic_year})
                                </p>
                                <p className="mt-2 text-xs text-plomo">
                                    {c.assignments_count} tareas · {c.exams_count} evaluaciones
                                </p>
                                <Link
                                    href={route('teacher.classrooms.show', c.id)}
                                    className="mt-3 inline-block text-sm font-medium text-brand-navy hover:underline"
                                >
                                    Entrar al aula →
                                </Link>
                            </Card>
                        ))
                    )}
                </div>
            </PageContainer>
        </TeacherLayout>
    );
}
