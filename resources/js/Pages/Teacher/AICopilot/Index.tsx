import AIAssistantPanel from '@/Components/AI/AIAssistantPanel';
import AIInsightCard from '@/Components/AI/AIInsightCard';
import AIRecommendationPanel from '@/Components/AI/AIRecommendationPanel';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import { getJson } from '@/utils/aiFetch';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

type Predictive = {
    students_visible: number;
    high_risk: number;
    flags: Record<string, number>;
    suggested_interventions: string[];
};

type Props = PageProps<{
    ai_enabled: boolean;
    modules: Record<string, boolean>;
    provider: string;
}>;

export default function AICopilotIndex() {
    const { ai_enabled, provider } = usePage<Props>().props;
    const [predictive, setPredictive] = useState<Predictive | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        getJson<Predictive>(route('teacher.ai-copilot.predictive'))
            .then(setPredictive)
            .catch(() => setPredictive(null))
            .finally(() => setLoading(false));
    }, []);

    return (
        <TeacherLayout title="Copiloto IA">
            <Head title="Copiloto IA docente" />
            <PageContainer>
                <SectionTitle
                    title="Copiloto pedagógico"
                    description={`Asistente institucional para planificar, evaluar y generar recursos. Proveedor: ${provider}.`}
                />

                {!ai_enabled && (
                    <AIInsightCard
                        title="IA en modo local"
                        body="La IA generativa está deshabilitada; las herramientas usan plantillas pedagógicas locales seguras hasta que un administrador active el proveedor."
                        badge="fallback"
                    />
                )}

                <div className="mb-8 grid gap-6 lg:grid-cols-3">
                    <div className="lg:col-span-2">
                        <AIAssistantPanel />
                    </div>
                    <AIRecommendationPanel data={predictive} loading={loading} />
                </div>
            </PageContainer>
        </TeacherLayout>
    );
}
