import AIGenerationWizard from '@/Components/AI/AIGenerationWizard';
import AIStreamingCard from '@/Components/AI/AIStreamingCard';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import { postJson } from '@/utils/aiFetch';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';

type Subject = { id: number; name: string };

type GenResult = {
    success: boolean;
    data: { questions?: Array<Record<string, unknown>> } | null;
    fallback?: boolean;
    cached?: boolean;
};

type Props = PageProps<{
    subjects: Subject[];
    ai_enabled: boolean;
}>;

export default function AICopilotExams() {
    const { subjects, ai_enabled } = usePage<Props>().props;
    const [step, setStep] = useState(0);
    const [topic, setTopic] = useState('');
    const [count, setCount] = useState(5);
    const [subjectId, setSubjectId] = useState<number | ''>(subjects[0]?.id ?? '');
    const [loading, setLoading] = useState(false);
    const [result, setResult] = useState<GenResult | null>(null);
    const [exportMsg, setExportMsg] = useState('');

    const generate = async () => {
        setLoading(true);
        setExportMsg('');
        try {
            const res = await postJson<GenResult>(route('teacher.ai-copilot.exams.generate'), {
                topic,
                question_count: count,
                difficulty: 'intermediate',
                question_types: 'multiple_choice',
            });
            setResult(res);
            setStep(1);
        } finally {
            setLoading(false);
        }
    };

    const exportBank = async () => {
        if (!subjectId || !result?.data?.questions) return;
        setLoading(true);
        try {
            const exp = await postJson<{ created: number }>(route('teacher.ai-copilot.exams.export'), {
                subject_id: subjectId,
                questions: result.data.questions,
            });
            setExportMsg(`Exportadas ${exp.created} preguntas al banco.`);
            setStep(2);
        } finally {
            setLoading(false);
        }
    };

    return (
        <TeacherLayout title="Generador de exámenes IA">
            <Head title="Exámenes IA" />
            <PageContainer>
                <SectionTitle
                    title="Generador de exámenes"
                    description="Preguntas alineadas a competencias; exportables al banco adaptativo."
                />

                <AIGenerationWizard steps={['Configurar', 'Revisar', 'Exportar']} currentStep={step}>
                    <div className="grid gap-6 lg:grid-cols-2">
                        <div className="space-y-4 rounded-xl border border-slate-200 bg-white p-4">
                            <label className="block text-xs font-semibold uppercase text-plomo">Tema</label>
                            <input
                                value={topic}
                                onChange={(e) => setTopic(e.target.value)}
                                className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                placeholder="Ej. fracciones equivalentes"
                            />
                            <label className="block text-xs font-semibold uppercase text-plomo">Cantidad</label>
                            <input
                                type="number"
                                min={1}
                                max={20}
                                value={count}
                                onChange={(e) => setCount(Number(e.target.value))}
                                className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                            />
                            <label className="block text-xs font-semibold uppercase text-plomo">Área / materia</label>
                            <select
                                value={subjectId}
                                onChange={(e) => setSubjectId(Number(e.target.value))}
                                className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                            >
                                {subjects.map((s) => (
                                    <option key={s.id} value={s.id}>
                                        {s.name}
                                    </option>
                                ))}
                            </select>
                            <button
                                type="button"
                                onClick={generate}
                                disabled={loading || !topic.trim()}
                                className="rounded-lg bg-navy px-4 py-2 text-sm font-semibold text-white disabled:opacity-50"
                            >
                                Generar preguntas
                            </button>
                            {!ai_enabled && (
                                <p className="text-xs text-amber-700">Modo plantilla local (IA deshabilitada).</p>
                            )}
                        </div>

                        <AIStreamingCard title="Vista previa" loading={loading}>
                            {result?.data?.questions?.map((q, i) => (
                                <div key={i} className="mb-4 border-b border-slate-100 pb-3">
                                    <p className="font-medium text-navy">
                                        {i + 1}. {(q.stem as string) ?? (q.question as string)}
                                    </p>
                                    {Array.isArray(q.options) && (
                                        <ul className="mt-1 list-inside list-disc text-xs text-plomo">
                                            {(q.options as Array<{ body?: string } | string>).map((o, j) => (
                                                <li key={j}>{typeof o === 'string' ? o : o.body}</li>
                                            ))}
                                        </ul>
                                    )}
                                </div>
                            ))}
                            {result?.data?.questions && (
                                <button
                                    type="button"
                                    onClick={exportBank}
                                    className="mt-2 rounded-lg border border-navy px-3 py-1.5 text-xs font-semibold text-navy"
                                >
                                    Exportar al banco de preguntas
                                </button>
                            )}
                            {exportMsg && <p className="mt-2 text-xs text-emerald-700">{exportMsg}</p>}
                        </AIStreamingCard>
                    </div>
                </AIGenerationWizard>
            </PageContainer>
        </TeacherLayout>
    );
}
