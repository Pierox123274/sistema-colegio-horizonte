import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { Loader2 } from 'lucide-react';
import { FormEvent, useState } from 'react';

type QOpt = { id: number; label: string | null; body: string };

type Question = {
    id: number;
    stem: string;
    topic: string;
    question_type: string;
    difficulty: string;
    competencies: string[];
    options: QOpt[];
};

type Props = PageProps<{
    attempt: {
        id: number;
        status: string;
        score_percent: number | null;
        classified_level: string | null;
        weakness_by_topic: Record<string, number>;
        exam: { title: string; mode: string };
    };
    question: Question | null;
    completed: boolean;
}>;

export default function DiagnosticAttempt() {
    const { attempt, question, completed } = usePage<Props>().props;
    const [answer, setAnswer] = useState<string | number | boolean>('');
    const [busy, setBusy] = useState(false);

    const submit = (e: FormEvent) => {
        e.preventDefault();
        if (completed || !question) return;
        setBusy(true);
        router.post(route('student.diagnostic.answer', attempt.id), { answer }, {
            onFinish: () => setBusy(false),
        });
    };

    return (
        <StudentLayout title={attempt.exam.title}>
            <Head title={attempt.exam.title} />
            <PageContainer>
                {completed ? (
                    <Card className="max-w-2xl border-l-4 border-l-emerald-500">
                        <h1 className="text-xl font-bold text-navy-900">Resultado del diagnóstico</h1>
                        <p className="mt-2 text-sm text-plomo">
                            Puntaje:{' '}
                            <strong>{attempt.score_percent ?? '—'}%</strong> — Nivel:{' '}
                            <strong className="capitalize">{attempt.classified_level ?? '—'}</strong>
                        </p>
                        <p className="mt-4 text-sm text-plomo">
                            Consulta tu ruta de aprendizaje y recomendaciones en el menú «Ruta de aprendizaje».
                        </p>
                    </Card>
                ) : !question ? (
                    <Card>
                        <p className="text-sm text-plomo">No hay pregunta disponible. Vuelva al inicio.</p>
                    </Card>
                ) : (
                    <form onSubmit={submit} className="max-w-2xl space-y-6">
                        <Card>
                            <p className="text-xs font-semibold uppercase text-plomo">
                                {question.topic} · {question.difficulty}
                            </p>
                            <p className="mt-3 text-base font-medium leading-relaxed text-navy-900">
                                {question.stem}
                            </p>
                            {question.question_type === 'multiple_choice' && (
                                <ul className="mt-4 space-y-2">
                                    {question.options.map((o) => (
                                        <li key={o.id}>
                                            <label className="flex cursor-pointer items-start gap-2 rounded-lg border border-slate-200 p-3 hover:bg-slate-50">
                                                <input
                                                    type="radio"
                                                    name="mc"
                                                    className="mt-1"
                                                    checked={answer === o.id}
                                                    onChange={() => setAnswer(o.id)}
                                                    required
                                                />
                                                <span className="text-sm">
                                                    <span className="font-semibold text-navy-900">
                                                        {o.label ?? ''}.{' '}
                                                    </span>
                                                    {o.body}
                                                </span>
                                            </label>
                                        </li>
                                    ))}
                                </ul>
                            )}
                            {question.question_type === 'true_false' && (
                                <div className="mt-4 flex gap-4">
                                    <label className="flex items-center gap-2 text-sm">
                                        <input
                                            type="radio"
                                            name="tf"
                                            checked={answer === true}
                                            onChange={() => setAnswer(true)}
                                        />
                                        Verdadero
                                    </label>
                                    <label className="flex items-center gap-2 text-sm">
                                        <input
                                            type="radio"
                                            name="tf"
                                            checked={answer === false}
                                            onChange={() => setAnswer(false)}
                                        />
                                        Falso
                                    </label>
                                </div>
                            )}
                            {question.question_type === 'short_answer' && (
                                <textarea
                                    className="mt-4 w-full rounded-lg border border-slate-300 p-3 text-sm"
                                    rows={3}
                                    value={typeof answer === 'string' ? answer : ''}
                                    onChange={(e) => setAnswer(e.target.value)}
                                    required
                                />
                            )}
                        </Card>
                        <button
                            type="submit"
                            disabled={busy}
                            className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-6 py-3 text-sm font-semibold text-white disabled:opacity-60"
                        >
                            {busy ? <Loader2 className="h-4 w-4 animate-spin" /> : null}
                            Enviar respuesta
                        </button>
                    </form>
                )}
            </PageContainer>
        </StudentLayout>
    );
}
