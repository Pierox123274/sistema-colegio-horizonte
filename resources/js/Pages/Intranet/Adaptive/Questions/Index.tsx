import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type Row = {
    id: number;
    topic: string;
    question_type: string;
    difficulty: string;
    is_active: boolean;
    subject: { name: string } | null;
};

type Props = PageProps<{
    questions: { data: Row[] };
}>;

export default function IntranetAdaptiveQuestionsIndex() {
    const { questions } = usePage<Props>().props;

    return (
        <IntranetLayout title="Banco de preguntas">
            <Head title="Banco de preguntas" />
            <PageContainer>
                <SectionTitle
                    title="Banco de preguntas"
                    description="Ítemes para diagnósticos fijos y adaptativos. La edición detallada se amplía según permisos."
                />
                <Card>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-left text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 text-xs uppercase text-plomo">
                                    <th className="py-3 pr-3 font-semibold">ID</th>
                                    <th className="py-3 pr-3 font-semibold">Tema</th>
                                    <th className="py-3 pr-3 font-semibold">Tipo</th>
                                    <th className="py-3 pr-3 font-semibold">Dificultad</th>
                                    <th className="py-3 pr-3 font-semibold">Curso</th>
                                    <th className="py-3 font-semibold">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                {questions.data.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="py-8 text-center text-plomo">
                                            Sin preguntas registradas.
                                        </td>
                                    </tr>
                                ) : (
                                    questions.data.map((q) => (
                                        <tr key={q.id} className="border-b border-slate-100">
                                            <td className="py-3 pr-3 font-mono text-xs">{q.id}</td>
                                            <td className="py-3 pr-3">{q.topic}</td>
                                            <td className="py-3 pr-3 capitalize text-plomo">{q.question_type.replaceAll('_', ' ')}</td>
                                            <td className="py-3 pr-3">
                                                <span className="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium capitalize">
                                                    {q.difficulty}
                                                </span>
                                            </td>
                                            <td className="py-3 pr-3 text-plomo">{q.subject?.name ?? '—'}</td>
                                            <td className="py-3">{q.is_active ? 'Activo' : 'Inactivo'}</td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
