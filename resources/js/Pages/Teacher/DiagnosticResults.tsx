import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type Row = { student_id: number; full_name: string; code: string; level: string | null; score: number | null };

type Props = PageProps<{
    low_students: Row[];
    weak_topics: Record<string, number>;
}>;

export default function DiagnosticResults() {
    const { low_students, weak_topics } = usePage<Props>().props;
    const topicEntries = Object.entries(weak_topics);

    return (
        <TeacherLayout title="Resultados diagnóstico">
            <Head title="Resultados diagnóstico" />
            <PageContainer>
                <SectionTitle
                    title="Resultados de diagnóstico"
                    description="Misma evidencia operativa que «Aprendizaje adaptativo», enfocada en seguimiento de evaluaciones."
                />
                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <h2 className="font-bold text-navy-900">Estudiantes con nivel bajo / sin diagnóstico</h2>
                        <ul className="mt-4 divide-y divide-slate-100">
                            {low_students.length === 0 ? (
                                <li className="py-2 text-sm text-plomo">Sin registros.</li>
                            ) : (
                                low_students.map((s) => (
                                    <li key={s.student_id} className="py-2 text-sm">
                                        <span className="font-medium">{s.full_name}</span>
                                        <span className="ml-2 text-plomo">
                                            ({s.code}) — nivel {s.level ?? '—'} — {s.score ?? '—'}%
                                        </span>
                                    </li>
                                ))
                            )}
                        </ul>
                    </Card>
                    <Card>
                        <h2 className="font-bold text-navy-900">Temas débiles (conteo agregado)</h2>
                        <ul className="mt-4 space-y-2">
                            {topicEntries.length === 0 ? (
                                <li className="text-sm text-plomo">Sin datos aún.</li>
                            ) : (
                                topicEntries.map(([t, n]) => (
                                    <li key={t} className="flex justify-between text-sm">
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
