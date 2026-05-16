import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type ExamRow = {
    id: number;
    title: string;
    mode: string;
    is_active: boolean;
    subject: string;
    section: string;
    attempts_count: number;
    questions_count: number;
};

type Props = PageProps<{
    exams: { data: ExamRow[]; links: unknown[]; meta?: { total?: number } };
    can_create: boolean;
}>;

export default function TeacherDiagnosticsIndex() {
    const { exams, can_create } = usePage<Props>().props;

    return (
        <TeacherLayout title="Diagnósticos">
            <Head title="Diagnósticos" />
            <PageContainer>
                <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <SectionTitle
                        title="Diagnósticos"
                        description="Exámenes de su sección o curso según asignaciones. Solo personal autorizado crea o activa diagnósticos; el estudiante solo los rinde."
                    />
                    {can_create ? (
                        <Link
                            href={route('teacher.diagnostics.create')}
                            className="inline-flex shrink-0 items-center justify-center rounded-lg bg-brand-navy px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-navy-800"
                        >
                            Crear diagnóstico
                        </Link>
                    ) : null}
                </div>

                <Card>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-left text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 text-xs uppercase text-plomo">
                                    <th className="py-3 pr-4 font-semibold">Título</th>
                                    <th className="py-3 pr-4 font-semibold">Curso</th>
                                    <th className="py-3 pr-4 font-semibold">Sección</th>
                                    <th className="py-3 pr-4 font-semibold">Modo</th>
                                    <th className="py-3 pr-4 font-semibold">Preguntas</th>
                                    <th className="py-3 pr-4 font-semibold">Intentos</th>
                                    <th className="py-3 pr-4 font-semibold">Estado</th>
                                    <th className="py-3 font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {exams.data.length === 0 ? (
                                    <tr>
                                        <td colSpan={8} className="py-8 text-center text-plomo">
                                            No hay diagnósticos visibles para su perfil.
                                        </td>
                                    </tr>
                                ) : (
                                    exams.data.map((e) => (
                                        <tr key={e.id} className="border-b border-slate-100">
                                            <td className="py-3 pr-4 font-medium text-navy-900">{e.title}</td>
                                            <td className="py-3 pr-4 text-plomo">{e.subject}</td>
                                            <td className="py-3 pr-4 text-plomo">{e.section}</td>
                                            <td className="py-3 pr-4 capitalize text-plomo">{e.mode}</td>
                                            <td className="py-3 pr-4">{e.questions_count}</td>
                                            <td className="py-3 pr-4">{e.attempts_count}</td>
                                            <td className="py-3 pr-4">
                                                <span
                                                    className={`rounded-full px-2 py-0.5 text-xs font-semibold ${
                                                        e.is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-600'
                                                    }`}
                                                >
                                                    {e.is_active ? 'Activo' : 'Inactivo'}
                                                </span>
                                            </td>
                                            <td className="py-3">
                                                <div className="flex flex-wrap gap-2">
                                                    <Link
                                                        href={route('teacher.diagnostics.show', e.id)}
                                                        className="font-medium text-brand-navy hover:underline"
                                                    >
                                                        Ver
                                                    </Link>
                                                    <Link
                                                        href={route('teacher.diagnostics.results', e.id)}
                                                        className="font-medium text-brand-navy hover:underline"
                                                    >
                                                        Resultados
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </Card>
            </PageContainer>
        </TeacherLayout>
    );
}
