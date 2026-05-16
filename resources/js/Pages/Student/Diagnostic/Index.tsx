import StudentPortalEmpty from '@/Components/Student/StudentPortalEmpty';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { ClipboardList, Play } from 'lucide-react';

type ExamRow = {
    id: number;
    title: string;
    description: string | null;
    mode: string;
    subject: { id: number; name: string } | null;
    prevent_retake: boolean;
};

type PortalCtx = {
    student: { id: number; full_name: string; code: string } | null;
    has_student: boolean;
    portal_scoped: boolean;
    empty_message: string;
};

type Props = PageProps<{
    portal: PortalCtx;
    exams: ExamRow[];
}>;

export default function DiagnosticIndex() {
    const { portal, exams } = usePage<Props>().props;

    return (
        <StudentLayout title="Diagnóstico adaptativo">
            <Head title="Diagnóstico adaptativo" />
            <PageContainer>
                <SectionTitle
                    title="Evaluación diagnóstica"
                    description="Determina tu nivel (básico, intermedio, avanzado) sin depender de servicios externos. Los resultados alimentan tu ruta de aprendizaje y recomendaciones."
                />
                {!portal.has_student ? (
                    <StudentPortalEmpty message={portal.empty_message} portalScoped={portal.portal_scoped} />
                ) : exams.length === 0 ? (
                    <Card>
                        <p className="text-sm text-plomo">Aún no hay exámenes diagnósticos publicados.</p>
                    </Card>
                ) : (
                    <ul className="grid gap-4 md:grid-cols-2">
                        {exams.map((e) => (
                            <li key={e.id}>
                                <Card className="h-full border-l-4 border-l-brand-yellow">
                                    <div className="flex items-start justify-between gap-3">
                                        <div>
                                            <p className="text-xs font-semibold uppercase text-plomo">
                                                {e.subject?.name ?? 'Curso general'}
                                            </p>
                                            <h2 className="mt-1 text-lg font-bold text-navy-900">{e.title}</h2>
                                            <p className="mt-2 line-clamp-3 text-sm text-plomo">
                                                {e.description ?? 'Sin descripción.'}
                                            </p>
                                            <p className="mt-2 text-xs text-plomo">
                                                Modo: <span className="font-semibold">{e.mode}</span>
                                                {e.prevent_retake ? ' · Una sola vez' : ''}
                                            </p>
                                        </div>
                                        <ClipboardList className="h-8 w-8 shrink-0 text-navy-900 opacity-70" />
                                    </div>
                                    <Link
                                        href={route('student.diagnostic.show', e.id)}
                                        className="mt-4 inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                                    >
                                        <Play className="h-4 w-4" /> Detalle
                                    </Link>
                                </Card>
                            </li>
                        ))}
                    </ul>
                )}
            </PageContainer>
        </StudentLayout>
    );
}
