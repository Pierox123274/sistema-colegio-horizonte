import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';

type Props = PageProps<{
    filters: { student_id: string };
    catalog: { students: SelectOption[] };
    recent_records: Array<{
        id: number;
        score: string;
        student: { id: number; code: string; first_name: string; last_name: string } | null;
        evaluation: { title: string; period: string; subject: { name: string } | null } | null;
    }>;
}>;

export default function HistoryIndex() {
    const { filters, catalog, recent_records } = usePage<Props>().props;

    return (
        <IntranetLayout title="Historial académico">
            <Head title="Historial académico" />
            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Gestión académica' }, { label: 'Historial académico' }]} />
                <SectionTitle title="Historial académico por estudiante" description="Busca un estudiante y revisa su historial de notas." />
                <Card className="mb-6">
                    <div className="grid gap-3 md:grid-cols-[1fr_auto]">
                        <select
                            defaultValue={filters.student_id}
                            onChange={(e) => router.get(route('intranet.academic.grades.history.index'), { student_id: e.target.value }, { preserveState: true })}
                            className="rounded-md border border-plomo/20 px-3 py-2 text-sm"
                        >
                            <option value="">Seleccionar estudiante</option>
                            {catalog.students.map((student) => (
                                <option key={student.value} value={student.value}>{student.label}</option>
                            ))}
                        </select>
                        <div>
                            {filters.student_id ? (
                                <Link href={route('intranet.academic.grades.students.show', filters.student_id)} className="inline-flex rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white">
                                    Ver historial individual
                                </Link>
                            ) : null}
                        </div>
                    </div>
                </Card>

                <Card>
                    <h3 className="mb-3 text-sm font-semibold text-navy-900">Notas recientes</h3>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b border-plomo/15 text-left">
                                    <th className="px-3 py-2">Estudiante</th>
                                    <th className="px-3 py-2">Curso</th>
                                    <th className="px-3 py-2">Evaluación</th>
                                    <th className="px-3 py-2">Periodo</th>
                                    <th className="px-3 py-2">Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                {recent_records.map((record) => (
                                    <tr key={record.id} className="border-b border-plomo/10">
                                        <td className="px-3 py-2">{record.student ? `${record.student.last_name}, ${record.student.first_name}` : '-'}</td>
                                        <td className="px-3 py-2">{record.evaluation?.subject?.name ?? '-'}</td>
                                        <td className="px-3 py-2">{record.evaluation?.title ?? '-'}</td>
                                        <td className="px-3 py-2">{record.evaluation?.period ?? '-'}</td>
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

