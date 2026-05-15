import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';

type Props = PageProps<{
    filters: { academic_year_id: string; educational_level_id: string; grade_id: string; section_id: string; subject_id: string; period: string; evaluation_id: string; student_id: string };
    catalog: { academic_years: SelectOption[]; levels: SelectOption[]; grades: SelectOption[]; sections: SelectOption[]; subjects: SelectOption[]; periods: SelectOption[]; evaluations: SelectOption[]; students: SelectOption[] };
    metrics: { total_records: number; course_average: number; general_average: number };
    records: { data: Array<{ id: number; score: string; student: { code: string; first_name: string; last_name: string } | null; evaluation: { title: string; period: string; subject: { name: string } | null; section: { name: string } | null } | null }> };
}>;

export default function ReportsIndex() {
    const { filters, catalog, metrics, records } = usePage<Props>().props;
    const queryString = new URLSearchParams(filters).toString();

    return (
        <IntranetLayout title="Reportes académicos">
            <Head title="Reportes académicos" />
            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Gestión académica' }, { label: 'Reportes académicos' }]} />
                <SectionTitle title="Reportes académicos" description="Filtra información académica y exporta PDF/CSV." />

                <Card className="mb-6">
                    <div className="grid gap-3 md:grid-cols-3">
                        <select defaultValue={filters.academic_year_id} onChange={(e) => router.get(route('intranet.academic.grades.reports.index'), { ...filters, academic_year_id: e.target.value }, { preserveState: true })} className="rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Año académico</option>{catalog.academic_years.map((item) => <option key={item.value} value={item.value}>{item.label}</option>)}</select>
                        <select defaultValue={filters.educational_level_id} onChange={(e) => router.get(route('intranet.academic.grades.reports.index'), { ...filters, educational_level_id: e.target.value }, { preserveState: true })} className="rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Nivel</option>{catalog.levels.map((item) => <option key={item.value} value={item.value}>{item.label}</option>)}</select>
                        <select defaultValue={filters.grade_id} onChange={(e) => router.get(route('intranet.academic.grades.reports.index'), { ...filters, grade_id: e.target.value }, { preserveState: true })} className="rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Grado</option>{catalog.grades.map((item) => <option key={item.value} value={item.value}>{item.label}</option>)}</select>
                        <select defaultValue={filters.section_id} onChange={(e) => router.get(route('intranet.academic.grades.reports.index'), { ...filters, section_id: e.target.value }, { preserveState: true })} className="rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Sección</option>{catalog.sections.map((item) => <option key={item.value} value={item.value}>{item.label}</option>)}</select>
                        <select defaultValue={filters.subject_id} onChange={(e) => router.get(route('intranet.academic.grades.reports.index'), { ...filters, subject_id: e.target.value }, { preserveState: true })} className="rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Curso</option>{catalog.subjects.map((item) => <option key={item.value} value={item.value}>{item.label}</option>)}</select>
                        <select defaultValue={filters.period} onChange={(e) => router.get(route('intranet.academic.grades.reports.index'), { ...filters, period: e.target.value }, { preserveState: true })} className="rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Periodo</option>{catalog.periods.map((item) => <option key={item.value} value={item.value}>{item.label}</option>)}</select>
                        <select defaultValue={filters.evaluation_id} onChange={(e) => router.get(route('intranet.academic.grades.reports.index'), { ...filters, evaluation_id: e.target.value }, { preserveState: true })} className="rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Evaluación</option>{catalog.evaluations.map((item) => <option key={item.value} value={item.value}>{item.label}</option>)}</select>
                        <select defaultValue={filters.student_id} onChange={(e) => router.get(route('intranet.academic.grades.reports.index'), { ...filters, student_id: e.target.value }, { preserveState: true })} className="rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Estudiante</option>{catalog.students.map((item) => <option key={item.value} value={item.value}>{item.label}</option>)}</select>
                        <div className="flex gap-2">
                            <a href={`${route('intranet.academic.grades.reports.export.pdf')}?${queryString}`} className="rounded-md border border-plomo/20 px-3 py-2 text-sm font-semibold">Exportar PDF</a>
                            <a href={`${route('intranet.academic.grades.reports.export.excel')}?${queryString}`} className="rounded-md border border-plomo/20 px-3 py-2 text-sm font-semibold">Exportar CSV</a>
                        </div>
                    </div>
                </Card>

                <div className="mb-4 grid gap-3 md:grid-cols-3">
                    <Card><p className="text-xs uppercase text-plomo">Registros</p><p className="text-2xl font-bold text-navy-900">{metrics.total_records}</p></Card>
                    <Card><p className="text-xs uppercase text-plomo">Promedio curso</p><p className="text-2xl font-bold text-navy-900">{metrics.course_average}</p></Card>
                    <Card><p className="text-xs uppercase text-plomo">Promedio general</p><p className="text-2xl font-bold text-navy-900">{metrics.general_average}</p></Card>
                </div>

                <Card>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b border-plomo/15 text-left">
                                    <th className="px-3 py-2">Estudiante</th>
                                    <th className="px-3 py-2">Curso</th>
                                    <th className="px-3 py-2">Evaluación</th>
                                    <th className="px-3 py-2">Periodo</th>
                                    <th className="px-3 py-2">Sección</th>
                                    <th className="px-3 py-2">Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                {records.data.map((record) => (
                                    <tr key={record.id} className="border-b border-plomo/10">
                                        <td className="px-3 py-2">{record.student ? `${record.student.last_name}, ${record.student.first_name}` : '-'}</td>
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

