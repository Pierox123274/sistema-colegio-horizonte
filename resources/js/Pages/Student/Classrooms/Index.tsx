import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type Props = PageProps<{
    classrooms: { data: { id: number; title: string; subject: string | null; section: string | null }[] };
}>;

export default function StudentClassroomsIndex() {
    const { classrooms } = usePage<Props>().props;

    return (
        <StudentLayout title="Aula virtual">
            <Head title="Aula virtual" />
            <PageContainer>
                <SectionTitle title="Mis aulas virtuales" description="Tareas, materiales y evaluaciones de tus cursos." />
                <div className="grid gap-4 md:grid-cols-2">
                    {classrooms.data.map((c) => (
                        <Card key={c.id}>
                            <h2 className="font-bold text-navy-900">{c.title}</h2>
                            <p className="text-sm text-plomo">
                                {c.subject} — {c.section}
                            </p>
                            <Link
                                href={route('student.classrooms.show', c.id)}
                                className="mt-3 inline-block text-sm font-medium text-brand-navy hover:underline"
                            >
                                Entrar →
                            </Link>
                        </Card>
                    ))}
                </div>
            </PageContainer>
        </StudentLayout>
    );
}
