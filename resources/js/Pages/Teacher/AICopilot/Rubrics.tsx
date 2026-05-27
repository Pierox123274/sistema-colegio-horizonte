import AIGenerationWizard from '@/Components/AI/AIGenerationWizard';
import AIStreamingCard from '@/Components/AI/AIStreamingCard';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import { postJson } from '@/utils/aiFetch';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';

type RubricLevel = { label: string; descriptor: string };
type RubricCriterion = { name: string; weight: number; levels: RubricLevel[] };

type GenResult = {
    success: boolean;
    data: { title?: string; criteria?: RubricCriterion[] } | null;
};

type Props = PageProps<{ ai_enabled: boolean }>;

export default function AICopilotRubrics() {
    const { ai_enabled } = usePage<Props>().props;
    const [title, setTitle] = useState('');
    const [activity, setActivity] = useState('');
    const [loading, setLoading] = useState(false);
    const [result, setResult] = useState<GenResult | null>(null);

    const generate = async () => {
        setLoading(true);
        try {
            const res = await postJson<GenResult>(route('teacher.ai-copilot.rubrics.generate'), {
                title,
                activity,
            });
            setResult(res);
        } finally {
            setLoading(false);
        }
    };

    const data = result?.data;

    return (
        <TeacherLayout title="Rúbricas IA">
            <Head title="Rúbricas IA" />
            <PageContainer>
                <SectionTitle title="Generador de rúbricas" description="Criterios, niveles y ponderaciones." />

                <AIGenerationWizard steps={['Actividad', 'Rúbrica']} currentStep={data ? 1 : 0}>
                    <div className="grid gap-6 lg:grid-cols-2">
                        <div className="space-y-3 rounded-xl border bg-white p-4">
                            <input
                                value={title}
                                onChange={(e) => setTitle(e.target.value)}
                                placeholder="Título de la rúbrica"
                                className="w-full rounded-lg border px-3 py-2 text-sm"
                            />
                            <textarea
                                value={activity}
                                onChange={(e) => setActivity(e.target.value)}
                                placeholder="Descripción de la actividad"
                                rows={3}
                                className="w-full rounded-lg border px-3 py-2 text-sm"
                            />
                            <button
                                type="button"
                                onClick={generate}
                                disabled={loading || !title.trim()}
                                className="rounded-lg bg-navy px-4 py-2 text-sm font-semibold text-white"
                            >
                                Generar rúbrica
                            </button>
                            {!ai_enabled && <p className="text-xs text-amber-700">Fallback local disponible.</p>}
                        </div>

                        <AIStreamingCard title="Rúbrica generada" loading={loading}>
                            {data?.criteria?.map((c) => (
                                <div key={c.name} className="mb-4 border-b pb-3">
                                    <p className="font-semibold text-navy">
                                        {c.name} ({c.weight}%)
                                    </p>
                                    <ul className="mt-1 space-y-1 text-xs text-plomo">
                                        {c.levels.map((l) => (
                                            <li key={l.label}>
                                                <strong>{l.label}:</strong> {l.descriptor}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            ))}
                        </AIStreamingCard>
                    </div>
                </AIGenerationWizard>
            </PageContainer>
        </TeacherLayout>
    );
}
