import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';

type Props = PageProps<{
    classroom: { id: number; title: string; description: string | null; subject: string | null; section: string | null };
    assignments: { id: number; title: string; due_at: string | null; max_score: number }[];
    exams: { id: number; title: string }[];
    pending_review_count: number;
}>;

export default function TeacherClassroomShow() {
    const { classroom, assignments, exams, pending_review_count } = usePage<Props>().props;

    const assignmentForm = useForm({
        title: '',
        description: '',
        max_score: 20,
        due_at: '',
    });

    const examForm = useForm({
        title: '',
        description: '',
        question_stem: '',
        correct_option: 'a',
        time_limit_minutes: 30,
        max_attempts: 1,
    });

    return (
        <TeacherLayout title={classroom.title}>
            <Head title={classroom.title} />
            <PageContainer>
                <div className="mb-4">
                    <Link href={route('teacher.classrooms.index')} className="text-sm text-brand-navy hover:underline">
                        ← Aulas
                    </Link>
                </div>
                <SectionTitle
                    title={classroom.title}
                    description={`${classroom.subject} — ${classroom.section}`}
                />
                <div className="mb-6 grid gap-4 sm:grid-cols-3">
                    <Card>
                        <p className="text-xs uppercase text-plomo">Entregas por revisar</p>
                        <p className="text-2xl font-bold text-navy-900">{pending_review_count}</p>
                    </Card>
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <h2 className="font-bold text-navy-900">Tareas</h2>
                        <ul className="mt-3 space-y-2 text-sm">
                            {assignments.map((a) => (
                                <li key={a.id} className="flex justify-between border-b border-slate-100 py-2">
                                    <span>{a.title}</span>
                                    <span className="text-plomo">{a.max_score} pts</span>
                                </li>
                            ))}
                        </ul>
                        <form
                            className="mt-4 space-y-2 border-t border-slate-100 pt-4"
                            onSubmit={(e) => {
                                e.preventDefault();
                                assignmentForm.post(route('teacher.classrooms.assignments.store', classroom.id));
                            }}
                        >
                            <input
                                placeholder="Nueva tarea"
                                className="w-full rounded border px-2 py-1 text-sm"
                                value={assignmentForm.data.title}
                                onChange={(e) => assignmentForm.setData('title', e.target.value)}
                                required
                            />
                            <button type="submit" className="rounded bg-brand-navy px-3 py-1.5 text-xs font-semibold text-white">
                                Crear tarea
                            </button>
                        </form>
                    </Card>
                    <Card>
                        <h2 className="font-bold text-navy-900">Evaluaciones online</h2>
                        <ul className="mt-3 space-y-2 text-sm">
                            {exams.map((e) => (
                                <li key={e.id}>{e.title}</li>
                            ))}
                        </ul>
                        <form
                            className="mt-4 space-y-2 border-t border-slate-100 pt-4"
                            onSubmit={(e) => {
                                e.preventDefault();
                                examForm.post(route('teacher.classrooms.exams.store', classroom.id));
                            }}
                        >
                            <input
                                placeholder="Título evaluación"
                                className="w-full rounded border px-2 py-1 text-sm"
                                value={examForm.data.title}
                                onChange={(e) => examForm.setData('title', e.target.value)}
                                required
                            />
                            <input
                                placeholder="Pregunta (opción múltiple)"
                                className="w-full rounded border px-2 py-1 text-sm"
                                value={examForm.data.question_stem}
                                onChange={(e) => examForm.setData('question_stem', e.target.value)}
                                required
                            />
                            <button type="submit" className="rounded bg-brand-navy px-3 py-1.5 text-xs font-semibold text-white">
                                Crear evaluación
                            </button>
                        </form>
                    </Card>
                </div>
            </PageContainer>
        </TeacherLayout>
    );
}
