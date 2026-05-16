import StudentPortalEmpty from '@/Components/Student/StudentPortalEmpty';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight, Sparkles } from 'lucide-react';

type PortalCtx = {
    student: { id: number; full_name: string; code: string } | null;
    has_student: boolean;
    portal_scoped: boolean;
    empty_message: string;
};

type Insight = {
    risk: { level: string; score: number; flags: string[] };
    recommendations: string[];
    ai_summary: string | null;
    generated_at: string;
} | null;

type RecProps = PageProps<{
    portal: PortalCtx;
    insight: Insight;
    ai_enabled: boolean;
}>;

export default function Recommendations() {
    const { portal, insight, ai_enabled } = usePage<RecProps>().props;

    return (
        <StudentLayout title="Recomendaciones IA">
            <Head title="Recomendaciones IA" />
            <PageContainer>
                <SectionTitle
                    title="Recomendaciones y resumen"
                    description="Sugerencias pedagógicas basadas en tus datos académicos agregados. No constituyen diagnóstico ni consejería clínica."
                />

                {!portal.has_student ? (
                    <StudentPortalEmpty message={portal.empty_message} portalScoped={portal.portal_scoped} />
                ) : (
                    <>
                        <div className="mb-4 flex flex-wrap gap-3">
                            <Link
                                href={route('student.ai-tutor.index')}
                                className="inline-flex items-center gap-2 rounded-lg bg-navy px-4 py-2 text-sm font-semibold text-white"
                            >
                                Ir al tutor IA <ArrowRight className="h-4 w-4" />
                            </Link>
                            {!ai_enabled && (
                                <span className="text-sm text-amber-800">
                                    Resumen IA con modelo externo desactivado; solo reglas locales.
                                </span>
                            )}
                        </div>

                        {insight?.ai_summary && (
                            <Card className="mb-6 border-l-4 border-l-brand-yellow">
                                <h3 className="mb-2 text-sm font-semibold text-navy">Resumen IA</h3>
                                <p className="whitespace-pre-wrap text-sm leading-relaxed text-slate-700">
                                    {insight.ai_summary}
                                </p>
                                <p className="mt-2 text-xs text-plomo">
                                    Generado: {new Date(insight.generated_at).toLocaleString()}
                                </p>
                            </Card>
                        )}

                        <div className="grid gap-4 md:grid-cols-2">
                            {(insight?.recommendations ?? []).map((tip, i) => (
                                <Card key={i} className="flex gap-3 border-l-4 border-l-navy/20">
                                    <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-yellow/30 text-sm font-bold text-navy">
                                        {i + 1}
                                    </div>
                                    <p className="text-sm leading-relaxed text-slate-700">{tip}</p>
                                </Card>
                            ))}
                        </div>
                    </>
                )}
            </PageContainer>
        </StudentLayout>
    );
}
