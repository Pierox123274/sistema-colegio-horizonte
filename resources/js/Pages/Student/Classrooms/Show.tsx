import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';

type Assignment = { id: number; title: string; status: string; max_score: number; score: number | null };

type Props = PageProps<{
    classroom: { id: number; title: string; subject: string | null };
    assignments: Assignment[];
    exams: { id: number; title: string }[];
}>;

function statusBadge(status: string) {
    const map: Record<string, string> = {
        pending: 'bg-amber-50 text-amber-900',
        submitted: 'bg-sky-50 text-sky-800',
        reviewed: 'bg-emerald-50 text-emerald-800',
        overdue: 'bg-rose-50 text-rose-800',
    };
    return (
        <span className={`rounded-full px-2 py-0.5 text-xs font-medium ${map[status] ?? 'bg-slate-100'}`}>
            {status}
        </span>
    );
}

export default function StudentClassroomShow() {
    const { classroom, assignments, exams } = usePage<Props>().props;

    const form = useForm({ student_comment: '' });

    return (
        <StudentLayout title={classroom.title}>
            <Head title={classroom.title} />
            <PageContainer>
                <Link href={route('student.classrooms.index')} className="text-sm text-brand-navy hover:underline">
                    ← Aulas
                </Link>
                <div className="mt-4">
                    <SectionTitle title={classroom.title} description={classroom.subject ?? ''} />
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <h2 className="font-bold text-navy-900">Tareas</h2>
                        <ul className="mt-3 space-y-3">
                            {assignments.map((a) => (
                                <li key={a.id} className="rounded-lg border border-slate-100 p-3">
                                    <div className="flex items-center justify-between gap-2">
                                        <span className="font-medium">{a.title}</span>
                                        {statusBadge(a.status)}
                                    </div>
                                    {a.score !== null ? (
                                        <p className="mt-1 text-sm text-plomo">Nota: {a.score} / {a.max_score}</p>
                                    ) : null}
                                    {a.status === 'pending' || a.status === 'overdue' ? (
                                        <form
                                            className="mt-2"
                                            onSubmit={(e) => {
                                                e.preventDefault();
                                                form.post(
                                                    route('student.classrooms.assignments.submit', [
                                                        classroom.id,
                                                        a.id,
                                                    ]),
                                                );
                                            }}
                                        >
                                            <textarea
                                                className="w-full rounded border px-2 py-1 text-sm"
                                                placeholder="Comentario (opcional)"
                                                value={form.data.student_comment}
                                                onChange={(e) => form.setData('student_comment', e.target.value)}
                                            />
                                            <button
                                                type="submit"
                                                className="mt-2 rounded bg-brand-navy px-3 py-1.5 text-xs font-semibold text-white"
                                            >
                                                Entregar
                                            </button>
                                        </form>
                                    ) : null}
                                </li>
                            ))}
                        </ul>
                    </Card>
                    <Card>
                        <h2 className="font-bold text-navy-900">Evaluaciones</h2>
                        <ul className="mt-3 space-y-2">
                            {exams.map((e) => (
                                <li key={e.id} className="flex items-center justify-between text-sm">
                                    <span>{e.title}</span>
                                    <Link
                                        href={route('student.classrooms.exams.start', e.id)}
                                        method="post"
                                        as="button"
                                        className="font-medium text-brand-navy hover:underline"
                                    >
                                        Iniciar
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </Card>
                </div>
            </PageContainer>
        </StudentLayout>
    );
}
