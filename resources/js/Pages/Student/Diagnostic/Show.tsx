import StudentPortalEmpty from '@/Components/Student/StudentPortalEmpty';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { AlertTriangle, ArrowLeft, Play } from 'lucide-react';

type PortalCtx = {
    student: { id: number; full_name: string; code: string } | null;
    has_student: boolean;
    portal_scoped: boolean;
    empty_message: string;
};

type Props = PageProps<{
    portal: PortalCtx;
    exam: {
        id: number;
        title: string;
        description: string | null;
        mode: string;
        adaptive_question_count: number;
        subject: { name: string } | null;
    };
    can_start: boolean;
}>;

export default function DiagnosticShow() {
    const { portal, exam, can_start } = usePage<Props>().props;

    const onStart = () => {
        router.post(route('student.diagnostic.start', exam.id));
    };

    return (
        <StudentLayout title={exam.title}>
            <Head title={exam.title} />
            <PageContainer>
                <div className="mb-4">
                    <Link
                        href={route('student.diagnostic.index')}
                        className="inline-flex items-center gap-2 text-sm font-medium text-navy-900 hover:underline"
                    >
                        <ArrowLeft className="h-4 w-4" /> Volver al listado
                    </Link>
                </div>
                <SectionTitle
                    title={exam.title}
                    description={exam.description ?? 'Diagnóstico institucional adaptativo.'}
                />

                {!portal.has_student ? (
                    <StudentPortalEmpty message={portal.empty_message} portalScoped={portal.portal_scoped} />
                ) : (
                    <Card>
                        <p className="text-sm text-plomo">
                            Curso: <strong>{exam.subject?.name ?? '—'}</strong>
                        </p>
                        <p className="mt-2 text-sm text-plomo">
                            Preguntas adaptativas (si aplica): {exam.adaptive_question_count}
                        </p>
                        {!can_start && (
                            <div className="mt-4 flex items-start gap-2 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">
                                <AlertTriangle className="mt-0.5 h-5 w-5 shrink-0" />
                                Este diagnóstico no admite un nuevo intento (política institucional).
                            </div>
                        )}
                        <button
                            type="button"
                            onClick={onStart}
                            disabled={!can_start}
                            className="mt-6 inline-flex items-center gap-2 rounded-lg bg-navy-900 px-5 py-2.5 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:bg-slate-300"
                        >
                            <Play className="h-4 w-4" /> Comenzar
                        </button>
                    </Card>
                )}
            </PageContainer>
        </StudentLayout>
    );
}
