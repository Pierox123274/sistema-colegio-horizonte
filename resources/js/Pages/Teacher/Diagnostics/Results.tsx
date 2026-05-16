import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type Attempt = {
    student: string | null;
    code: string | null;
    score_percent: number | null;
    classified_level: string | null;
    completed_at: string | null;
};

type Props = PageProps<{
    exam: { id: number; title: string };
    attempts: Attempt[];
    weak_topics: Record<string, number>;
}>;

function levelBadge(level: string | null) {
    if (!level) {
        return <span className="text-plomo">—</span>;
    }
    const l = level.toLowerCase();
    const cls =
        l === 'advanced' || l === 'avanzado'
            ? 'bg-emerald-50 text-emerald-800'
            : l === 'intermediate' || l === 'intermedio'
              ? 'bg-sky-50 text-sky-800'
              : 'bg-amber-50 text-amber-900';
    return <span className={`rounded-full px-2 py-0.5 text-xs font-semibold capitalize ${cls}`}>{level}</span>;
}

export default function TeacherDiagnosticsResults() {
    const { exam, attempts, weak_topics } = usePage<Props>().props;
    const topics = Object.entries(weak_topics).slice(0, 12);

    return (
        <TeacherLayout title={`Resultados — ${exam.title}`}>
            <Head title={`Resultados — ${exam.title}`} />
            <PageContainer>
                <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <SectionTitle
                        title="Resultados del diagnóstico"
                        description={`${exam.title} — intentos completados visibles para su asignación.`}
                    />
                    <Link
                        href={route('teacher.diagnostics.show', exam.id)}
                        className="text-sm font-medium text-brand-navy hover:underline"
                    >
                        ← Volver al examen
                    </Link>
                </div>

                <div className="mb-6 grid gap-4 lg:grid-cols-3">
                    <Card className="lg:col-span-2">
                        <h2 className="font-bold text-navy-900">Intentos</h2>
                        <div className="mt-3 overflow-x-auto">
                            <table className="min-w-full text-left text-sm">
                                <thead>
                                    <tr className="border-b border-slate-200 text-xs uppercase text-plomo">
                                        <th className="py-2 pr-3 font-semibold">Estudiante</th>
                                        <th className="py-2 pr-3 font-semibold">Código</th>
                                        <th className="py-2 pr-3 font-semibold">Puntaje</th>
                                        <th className="py-2 pr-3 font-semibold">Nivel</th>
                                        <th className="py-2 font-semibold">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {attempts.length === 0 ? (
                                        <tr>
                                            <td colSpan={5} className="py-6 text-center text-plomo">
                                                Sin intentos completados aún.
                                            </td>
                                        </tr>
                                    ) : (
                                        attempts.map((a, idx) => (
                                            <tr key={`${a.code}-${idx}`} className="border-b border-slate-100">
                                                <td className="py-2 pr-3">{a.student ?? '—'}</td>
                                                <td className="py-2 pr-3 text-plomo">{a.code ?? '—'}</td>
                                                <td className="py-2 pr-3">{a.score_percent ?? '—'}%</td>
                                                <td className="py-2 pr-3">{levelBadge(a.classified_level)}</td>
                                                <td className="py-2 text-plomo">
                                                    {a.completed_at ? new Date(a.completed_at).toLocaleString() : '—'}
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </Card>
                    <Card>
                        <h2 className="font-bold text-navy-900">Temas débiles (agregado)</h2>
                        <ul className="mt-3 space-y-2 text-sm">
                            {topics.length === 0 ? (
                                <li className="text-plomo">Sin datos.</li>
                            ) : (
                                topics.map(([t, n]) => (
                                    <li key={t} className="flex justify-between gap-2">
                                        <span>{t}</span>
                                        <span className="font-semibold text-navy-900">{n}</span>
                                    </li>
                                ))
                            )}
                        </ul>
                    </Card>
                </div>
            </PageContainer>
        </TeacherLayout>
    );
}
