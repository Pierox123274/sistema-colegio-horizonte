import AIGenerationWizard from '@/Components/AI/AIGenerationWizard';
import AIStreamingCard from '@/Components/AI/AIStreamingCard';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import { postJson } from '@/utils/aiFetch';
import type { PageProps } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

type Classroom = { id: number; title: string };

type AssignmentPayload = {
    title?: string;
    instructions?: string;
    objectives?: string[];
    criteria?: string[];
    resources?: string[];
    estimated_minutes?: number;
};

type GenResult = {
    success: boolean;
    data: AssignmentPayload | null;
};

type Props = PageProps<{
    classrooms: Classroom[];
    ai_enabled: boolean;
}>;

export default function AICopilotAssignments() {
    const { classrooms, ai_enabled } = usePage<Props>().props;
    const [topic, setTopic] = useState('');
    const [classroomId, setClassroomId] = useState<number | ''>(classrooms[0]?.id ?? '');
    const [loading, setLoading] = useState(false);
    const [result, setResult] = useState<GenResult | null>(null);
    const [step, setStep] = useState(0);

    const generate = async () => {
        setLoading(true);
        try {
            const res = await postJson<GenResult>(route('teacher.ai-copilot.assignments.generate'), {
                topic,
            });
            setResult(res);
            setStep(1);
        } finally {
            setLoading(false);
        }
    };

    const exportLms = async () => {
        if (!classroomId || !result?.data) return;
        setLoading(true);
        try {
            const exp = await postJson<{ redirect: string }>(
                route('teacher.ai-copilot.assignments.export'),
                { virtual_classroom_id: classroomId, payload: result.data },
            );
            router.visit(exp.redirect);
        } finally {
            setLoading(false);
        }
    };

    const data = result?.data;

    return (
        <TeacherLayout title="Generador de tareas IA">
            <Head title="Tareas IA" />
            <PageContainer>
                <SectionTitle title="Generador de tareas" description="Exporta a aula virtual LMS." />

                <AIGenerationWizard steps={['Tema', 'Revisar', 'LMS']} currentStep={step}>
                    <div className="grid gap-6 lg:grid-cols-2">
                        <div className="space-y-3 rounded-xl border border-slate-200 bg-white p-4">
                            <input
                                value={topic}
                                onChange={(e) => setTopic(e.target.value)}
                                placeholder="Tema de la tarea"
                                className="w-full rounded-lg border px-3 py-2 text-sm"
                            />
                            <select
                                value={classroomId}
                                onChange={(e) => setClassroomId(Number(e.target.value))}
                                className="w-full rounded-lg border px-3 py-2 text-sm"
                            >
                                {classrooms.map((c) => (
                                    <option key={c.id} value={c.id}>
                                        {c.title}
                                    </option>
                                ))}
                            </select>
                            <button
                                type="button"
                                onClick={generate}
                                disabled={loading || !topic.trim()}
                                className="rounded-lg bg-navy px-4 py-2 text-sm font-semibold text-white"
                            >
                                Generar tarea
                            </button>
                            {!ai_enabled && (
                                <p className="text-xs text-amber-700">Plantilla local si IA está off.</p>
                            )}
                        </div>

                        <AIStreamingCard title="Propuesta" loading={loading}>
                            {data && (
                                <>
                                    <h4 className="font-semibold text-navy">{data.title}</h4>
                                    <p className="mt-2 whitespace-pre-wrap">{data.instructions}</p>
                                    {data.objectives && (
                                        <ul className="mt-2 list-disc pl-4 text-xs">
                                            {data.objectives.map((o) => (
                                                <li key={o}>{o}</li>
                                            ))}
                                        </ul>
                                    )}
                                    <button
                                        type="button"
                                        onClick={exportLms}
                                        className="mt-4 rounded-lg border border-navy px-3 py-1.5 text-xs font-semibold"
                                    >
                                        Crear en aula virtual
                                    </button>
                                </>
                            )}
                        </AIStreamingCard>
                    </div>
                </AIGenerationWizard>
            </PageContainer>
        </TeacherLayout>
    );
}
