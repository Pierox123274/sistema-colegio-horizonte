import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type Exam = {
    id: number;
    title: string;
    description: string | null;
    mode: string;
    is_active: boolean;
    subject: string | null;
    section: string | null;
    academic_year: string | null;
    adaptive_question_count: number;
    threshold_basic_percent: number;
    threshold_intermediate_percent: number;
    questions_count: number;
};

type Props = PageProps<{
    exam: Exam;
    results_href: string;
}>;

export default function TeacherDiagnosticsShow() {
    const { exam, results_href } = usePage<Props>().props;

    return (
        <TeacherLayout title={exam.title}>
            <Head title={exam.title} />
            <PageContainer>
                <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <SectionTitle title={exam.title} description={exam.description ?? 'Detalle del diagnóstico.'} />
                    <Link
                        href={results_href}
                        className="inline-flex shrink-0 items-center justify-center rounded-lg bg-brand-navy px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-navy-800"
                    >
                        Ver resultados
                    </Link>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    <Card>
                        <h2 className="font-bold text-navy-900">Alcance</h2>
                        <dl className="mt-3 space-y-2 text-sm">
                            <div className="flex justify-between gap-4">
                                <dt className="text-plomo">Año</dt>
                                <dd className="font-medium">{exam.academic_year ?? '—'}</dd>
                            </div>
                            <div className="flex justify-between gap-4">
                                <dt className="text-plomo">Sección</dt>
                                <dd className="font-medium">{exam.section ?? '—'}</dd>
                            </div>
                            <div className="flex justify-between gap-4">
                                <dt className="text-plomo">Curso</dt>
                                <dd className="font-medium">{exam.subject ?? '—'}</dd>
                            </div>
                        </dl>
                    </Card>
                    <Card>
                        <h2 className="font-bold text-navy-900">Configuración</h2>
                        <dl className="mt-3 space-y-2 text-sm">
                            <div className="flex justify-between gap-4">
                                <dt className="text-plomo">Modo</dt>
                                <dd className="font-medium capitalize">{exam.mode}</dd>
                            </div>
                            <div className="flex justify-between gap-4">
                                <dt className="text-plomo">Estado</dt>
                                <dd>
                                    <span
                                        className={`rounded-full px-2 py-0.5 text-xs font-semibold ${
                                            exam.is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-600'
                                        }`}
                                    >
                                        {exam.is_active ? 'Activo' : 'Inactivo'}
                                    </span>
                                </dd>
                            </div>
                            <div className="flex justify-between gap-4">
                                <dt className="text-plomo">Preguntas enlazadas</dt>
                                <dd className="font-medium">{exam.questions_count}</dd>
                            </div>
                            <div className="flex justify-between gap-4">
                                <dt className="text-plomo">Preguntas adaptativo</dt>
                                <dd className="font-medium">{exam.adaptive_question_count}</dd>
                            </div>
                            <div className="flex justify-between gap-4">
                                <dt className="text-plomo">Umbrales básico / intermedio</dt>
                                <dd className="font-medium">
                                    {exam.threshold_basic_percent}% / {exam.threshold_intermediate_percent}%
                                </dd>
                            </div>
                        </dl>
                    </Card>
                </div>

                <p className="mt-6 text-sm text-plomo">
                    La edición avanzada y el banco de preguntas se gestionan desde intranet (administración) o flujos
                    autorizados según políticas del colegio.
                </p>
            </PageContainer>
        </TeacherLayout>
    );
}
