import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type Exam = {
    id: number;
    title: string;
    description: string | null;
    mode: string;
    is_active: boolean;
    adaptive_question_count: number;
    threshold_basic_percent: number;
    threshold_intermediate_percent: number;
    prevent_retake_after_completion: boolean;
    subject: { id: number; name: string } | null;
    academic_year_id: number | null;
    section_id: number | null;
    grade_id: number | null;
    educational_level_id: number | null;
    attempts_count: number;
    questions_count: number;
};

type Props = PageProps<{ exam: Exam }>;

export default function IntranetDiagnosticExamsShow() {
    const { exam } = usePage<Props>().props;

    return (
        <IntranetLayout title={exam.title}>
            <Head title={exam.title} />
            <PageContainer>
                <div className="mb-6 flex justify-between gap-4">
                    <SectionTitle title={exam.title} description={exam.description ?? ''} />
                    <Link
                        href={route('intranet.adaptive.diagnostic-exams.edit', exam.id)}
                        className="h-fit rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium hover:bg-slate-50"
                    >
                        Editar
                    </Link>
                </div>
                <Card>
                    <dl className="grid gap-3 text-sm sm:grid-cols-2">
                        <div>
                            <dt className="text-plomo">Modo</dt>
                            <dd className="font-medium capitalize">{exam.mode}</dd>
                        </div>
                        <div>
                            <dt className="text-plomo">Activo</dt>
                            <dd className="font-medium">{exam.is_active ? 'Sí' : 'No'}</dd>
                        </div>
                        <div>
                            <dt className="text-plomo">Curso</dt>
                            <dd className="font-medium">{exam.subject?.name ?? '—'}</dd>
                        </div>
                        <div>
                            <dt className="text-plomo">IDs alcance</dt>
                            <dd className="font-mono text-xs text-plomo">
                                año {exam.academic_year_id ?? '—'} · sección {exam.section_id ?? '—'} · grado{' '}
                                {exam.grade_id ?? '—'} · nivel {exam.educational_level_id ?? '—'}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-plomo">Preguntas / intentos</dt>
                            <dd className="font-medium">
                                {exam.questions_count} / {exam.attempts_count}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-plomo">Reintento</dt>
                            <dd className="font-medium">{exam.prevent_retake_after_completion ? 'Bloqueado' : 'Permitido'}</dd>
                        </div>
                    </dl>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
