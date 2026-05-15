import StudentPortalEmpty from '@/Components/Student/StudentPortalEmpty';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type Props = PageProps<{
    student: { full_name: string; code: string } | null;
    history: {
        data: Array<{
            id: number;
            score: string;
            evaluation: {
                title: string;
                period: string;
                subject: { name: string } | null;
                section: { name: string } | null;
            } | null;
        }>;
    } | null;
    metrics: { course_average: number; general_average: number };
    has_student: boolean;
    portal_scoped: boolean;
    empty_message: string;
}>;

export default function StudentGradesIndex() {
    const { student, history, metrics, has_student, portal_scoped, empty_message } =
        usePage<Props>().props;

    return (
        <StudentLayout title="Mis notas">
            <Head title="Mis notas" />
            <PageContainer>
                <SectionTitle
                    title="Mis notas"
                    description={
                        student
                            ? `${student.full_name} · ${student.code}`
                            : 'Consulta de calificaciones (solo lectura).'
                    }
                />

                {!has_student || !history ? (
                    <StudentPortalEmpty message={empty_message} portalScoped={portal_scoped} />
                ) : (
                    <>
                        <div className="mb-6 grid gap-4 md:grid-cols-2">
                            <Card>
                                <p className="text-xs uppercase text-plomo">Promedio general</p>
                                <p className="text-2xl font-bold text-navy-900">{metrics.general_average}</p>
                            </Card>
                            <Card>
                                <p className="text-xs uppercase text-plomo">Promedio de registros</p>
                                <p className="text-2xl font-bold text-navy-900">{metrics.course_average}</p>
                            </Card>
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
                                                <td className="px-3 py-2">
                                                    {record.evaluation?.subject?.name ?? '—'}
                                                </td>
                                                <td className="px-3 py-2">
                                                    {record.evaluation?.title ?? '—'}
                                                </td>
                                                <td className="px-3 py-2">
                                                    {record.evaluation?.period ?? '—'}
                                                </td>
                                                <td className="px-3 py-2">
                                                    {record.evaluation?.section?.name ?? '—'}
                                                </td>
                                                <td className="px-3 py-2 font-semibold">{record.score}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </Card>
                    </>
                )}
            </PageContainer>
        </StudentLayout>
    );
}
