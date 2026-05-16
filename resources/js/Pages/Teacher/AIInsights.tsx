import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { Sparkles } from 'lucide-react';

type RiskRow = {
    student: { id: number; code: string; full_name: string };
    section: { id: number; name: string } | null | undefined;
    risk: {
        level: string;
        score: number;
        average: number | null;
        attendance_pct: number | null;
    };
};

type Props = PageProps<{
    risk_rows: RiskRow[];
    insight: {
        aggregate: Record<string, number>;
        ai_summary: string | null;
    };
    ai_enabled: boolean;
}>;

export default function AIInsights() {
    const { risk_rows, insight, ai_enabled } = usePage<Props>().props;

    return (
        <TeacherLayout title="IA — Insights docente">
            <Head title="Insights docente IA" />
            <PageContainer>
                <SectionTitle
                    title="Insights docente"
                    description="Vista agregada del alumnado de tus secciones (año activo). La IA complementa con sugerencias pedagógicas generales."
                />

                <div className="mb-6 grid gap-4 md:grid-cols-4">
                    <Card>
                        <p className="text-xs uppercase text-plomo">Total visible</p>
                        <p className="text-2xl font-bold text-navy">{insight.aggregate.total}</p>
                    </Card>
                    <Card className="border-l-4 border-l-red-400">
                        <p className="text-xs uppercase text-plomo">Riesgo alto</p>
                        <p className="text-2xl font-bold text-red-700">{insight.aggregate.alto}</p>
                    </Card>
                    <Card className="border-l-4 border-l-amber-400">
                        <p className="text-xs uppercase text-plomo">Riesgo medio</p>
                        <p className="text-2xl font-bold text-amber-800">{insight.aggregate.medio}</p>
                    </Card>
                    <Card className="border-l-4 border-l-emerald-400">
                        <p className="text-xs uppercase text-plomo">Riesgo bajo</p>
                        <p className="text-2xl font-bold text-emerald-800">{insight.aggregate.bajo}</p>
                    </Card>
                </div>

                {insight.ai_summary && (
                    <Card className="mb-6 border-l-4 border-l-brand-yellow">
                        <h3 className="mb-2 flex items-center gap-2 text-sm font-semibold text-navy">
                            <Sparkles className="h-4 w-4 text-brand-yellow" /> Sugerencias IA
                            {!ai_enabled && (
                                <span className="text-xs font-normal text-plomo">(histórico / caché)</span>
                            )}
                        </h3>
                        <p className="whitespace-pre-wrap text-sm text-slate-700">{insight.ai_summary}</p>
                    </Card>
                )}

                <Card>
                    <h3 className="mb-3 text-sm font-semibold text-navy">Avance rápido (primeros casos)</h3>
                    <ul className="space-y-2 text-sm text-slate-700">
                        {risk_rows.slice(0, 6).map((row) => (
                            <li key={row.student.id} className="flex justify-between gap-2 border-b border-slate-100 pb-2">
                                <span>
                                    <span className="font-medium">{row.student.full_name}</span>
                                    <span className="ml-2 text-plomo">({row.section?.name ?? '—'})</span>
                                </span>
                                <span className="shrink-0 capitalize text-xs font-semibold text-navy">
                                    {row.risk.level} · {row.risk.score}
                                </span>
                            </li>
                        ))}
                    </ul>
                </Card>
            </PageContainer>
        </TeacherLayout>
    );
}
