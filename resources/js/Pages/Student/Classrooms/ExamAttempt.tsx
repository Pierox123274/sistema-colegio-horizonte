import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/react';

type Q = { id: number; stem: string; options: { label: string; value: string }[] | null };

type Props = PageProps<{
    attempt: { id: number; status: string; score_percent: number | null; exam_title: string };
    questions: Q[];
    completed: boolean;
}>;

export default function StudentExamAttempt() {
    const { attempt, questions, completed } = usePage<Props>().props;
    const form = useForm<{ answers: Record<string, string> }>({ answers: {} });

    if (completed) {
        return (
            <StudentLayout title="Resultado">
                <Head title="Resultado evaluación" />
                <PageContainer>
                    <Card>
                        <h1 className="text-xl font-bold text-navy-900">{attempt.exam_title}</h1>
                        <p className="mt-4 text-lg">
                            Puntaje: <strong>{attempt.score_percent ?? '—'}%</strong>
                        </p>
                    </Card>
                </PageContainer>
            </StudentLayout>
        );
    }

    return (
        <StudentLayout title="Evaluación">
            <Head title="Evaluación online" />
            <PageContainer>
                <Card>
                    <h1 className="font-bold text-navy-900">{attempt.exam_title}</h1>
                    <form
                        className="mt-6 space-y-6"
                        onSubmit={(e) => {
                            e.preventDefault();
                            form.post(route('student.classrooms.exam-attempt.answer', attempt.id));
                        }}
                    >
                        {questions.map((q) => (
                            <div key={q.id}>
                                <p className="font-medium">{q.stem}</p>
                                <div className="mt-2 space-y-1">
                                    {(q.options ?? []).map((o) => (
                                        <label key={o.value} className="flex items-center gap-2 text-sm">
                                            <input
                                                type="radio"
                                                name={`q-${q.id}`}
                                                value={o.value}
                                                onChange={() =>
                                                    form.setData('answers', {
                                                        ...form.data.answers,
                                                        [q.id]: o.value,
                                                    })
                                                }
                                            />
                                            {o.label}
                                        </label>
                                    ))}
                                </div>
                            </div>
                        ))}
                        <button
                            type="submit"
                            className="rounded-lg bg-brand-navy px-4 py-2 text-sm font-semibold text-white"
                        >
                            Enviar evaluación
                        </button>
                    </form>
                </Card>
            </PageContainer>
        </StudentLayout>
    );
}
