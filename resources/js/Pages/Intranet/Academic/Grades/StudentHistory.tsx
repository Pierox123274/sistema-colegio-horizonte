import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type Props = PageProps<{
    student: { id: number; code: string; first_name: string; last_name: string; document_number: string | null };
    history: { data: Array<{ id: number; score: string; evaluation: { title: string; period: string; subject: { name: string } | null; section: { name: string } | null } | null }> };
    metrics: { course_average: number; general_average: number };
}>;

export default function GradeStudentHistory() {
    const { student, history, metrics } = usePage<Props>().props;

    return (
        <IntranetLayout title="Historial académico">
            <Head title="Historial académico" />
            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Gestión académica' }, { label: 'Historial académico' }]} />
                <SectionTitle title={`Historial académico - ${student.last_name}, ${student.first_name}`} description="Detalle de notas, promedio por curso y promedio general." actions={<Link href={route('intranet.academic.grades.records.index')} className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold">Volver</Link>} />
                <div className="mb-4 grid gap-3 md:grid-cols-2">
                    <Card><p className="text-xs uppercase text-plomo">Promedio curso</p><p className="text-2xl font-bold text-navy-900">{metrics.course_average}</p></Card>
                    <Card><p className="text-xs uppercase text-plomo">Promedio general</p><p className="text-2xl font-bold text-navy-900">{metrics.general_average}</p></Card>
                </div>
                <Card>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b border-plomo/15 text-left">
                                    <th className="px-3 py-2">Curso</th>
                                    <th className="px-3 py-2">Evaluación</th>
                                    <th className="px-3 py-2">Periodo</th>
                                    <th className="px-3 py-2">Sección</th>
                                    <th className="px-3 py-2">Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                {history.data.map((record) => (
                                    <tr key={record.id} className="border-b border-plomo/10">
                                        <td className="px-3 py-2">{record.evaluation?.subject?.name ?? '-'}</td>
                                        <td className="px-3 py-2">{record.evaluation?.title ?? '-'}</td>
                                        <td className="px-3 py-2">{record.evaluation?.period ?? '-'}</td>
                                        <td className="px-3 py-2">{record.evaluation?.section?.name ?? '-'}</td>
                                        <td className="px-3 py-2 font-semibold">{record.score}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}

