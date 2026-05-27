import AnalyticsBarChart from '@/Components/Analytics/AnalyticsBarChart';
import AnalyticsDonutChart from '@/Components/Analytics/AnalyticsDonutChart';
import AnalyticsFiltersBar from '@/Components/Analytics/AnalyticsFiltersBar';
import AnalyticsKpiCard from '@/Components/Analytics/AnalyticsKpiCard';
import AnalyticsLineChart from '@/Components/Analytics/AnalyticsLineChart';
import { AppCard } from '@/Components/App/AppCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { AppTable } from '@/Components/App/AppTable';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { AlertTriangle, CalendarCheck, TrendingUp } from 'lucide-react';

type ChartPoint = { label: string; value: number };

type Props = PageProps<{
    filters: { academic_year_id: string; date_from: string; date_to: string };
    catalog: { academic_years: SelectOption[] };
    has_assignments: boolean;
    academic: {
        summary: Record<string, number>;
        performance_by_section: { label: string; average: number }[];
        top_students: { student: string; code: string; average: number }[];
        risk_students: { student: string; code: string; average: number }[];
        attendance_trend: ChartPoint[];
        attendance_distribution: ChartPoint[];
        recent_evaluations: { id: number; title: string; section?: string; subject?: string; evaluated_at?: string }[];
        most_absences: { student: string; code: string; absences: number }[];
    };
}>;

export default function TeacherAnalyticsIndex() {
    const { filters, catalog, academic, has_assignments } = usePage<Props>().props;

    const sectionChart = academic.performance_by_section.map((s) => ({
        label: s.label,
        value: s.average,
    }));

    return (
        <TeacherLayout title="Analítica docente">
            <Head title="Analítica docente" />
            <PageContainer>
                <AppPageHeader
                    title="Analítica de mis secciones"
                    description="Indicadores de asistencia, notas y evaluaciones según sus asignaciones."
                />

                {!has_assignments ? (
                    <AppCard>
                        <p className="text-sm text-plomo">
                            No tiene asignaciones activas en el año académico vigente. Los indicadores
                            aparecerán cuando administración registre su carga.
                        </p>
                    </AppCard>
                ) : (
                    <>
                        <AnalyticsFiltersBar
                            filters={filters}
                            catalog={catalog}
                            routeName="teacher.analytics.index"
                        />

                        <div className="mb-6 grid gap-4 sm:grid-cols-3">
                            <AnalyticsKpiCard
                                title="Estudiantes"
                                value={academic.summary.total_students}
                            />
                            <AnalyticsKpiCard
                                title="Asistencia promedio"
                                value={`${academic.summary.attendance_average}%`}
                                icon={CalendarCheck}
                                accent="yellow"
                            />
                            <AnalyticsKpiCard
                                title="Promedio general"
                                value={academic.summary.institutional_average}
                                icon={TrendingUp}
                            />
                            <AnalyticsKpiCard
                                title="En riesgo"
                                value={academic.summary.risk_students_count}
                                icon={AlertTriangle}
                                accent="red"
                            />
                        </div>

                        <div className="mb-6 grid gap-6 lg:grid-cols-2">
                            <AnalyticsBarChart title="Promedio por sección" data={sectionChart} />
                            <AnalyticsLineChart title="Asistencia (registros)" data={academic.attendance_trend} />
                        </div>

                        <div className="grid gap-6 lg:grid-cols-2">
                            <AnalyticsDonutChart
                                title="Estados de asistencia"
                                data={academic.attendance_distribution}
                            />
                            <AppCard>
                                <h3 className="mb-3 text-sm font-bold text-navy-900">Evaluaciones recientes</h3>
                                <ul className="space-y-2 text-sm">
                                    {academic.recent_evaluations.map((ev) => (
                                        <li key={ev.id} className="rounded-lg border border-plomo/10 px-3 py-2">
                                            <p className="font-semibold text-navy-900">{ev.title}</p>
                                            <p className="text-plomo">
                                                {ev.section} · {ev.subject} · {ev.evaluated_at}
                                            </p>
                                        </li>
                                    ))}
                                </ul>
                            </AppCard>
                        </div>

                        <div className="mt-6 grid gap-6 lg:grid-cols-2">
                            <AppTable title="Mayor número de faltas">
                                <h3 className="mb-3 text-sm font-bold text-navy-900">Mayor número de faltas</h3>
                                <table className="min-w-full text-sm">
                                    <tbody>
                                        {academic.most_absences.map((row) => (
                                            <tr key={row.code} className="border-b border-plomo/10">
                                                <td className="py-2">{row.student}</td>
                                                <td className="py-2 text-right font-semibold">{row.absences}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </AppTable>
                            <AppTable title="Bajo rendimiento">
                                <h3 className="mb-3 text-sm font-bold text-navy-900">Bajo rendimiento</h3>
                                <table className="min-w-full text-sm">
                                    <tbody>
                                        {academic.risk_students.map((row) => (
                                            <tr key={row.code} className="border-b border-plomo/10">
                                                <td className="py-2">{row.student}</td>
                                                <td className="py-2 text-right font-semibold">{row.average}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </AppTable>
                        </div>
                    </>
                )}
            </PageContainer>
        </TeacherLayout>
    );
}
