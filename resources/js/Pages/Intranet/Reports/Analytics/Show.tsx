import AnalyticsBarChart from '@/Components/Analytics/AnalyticsBarChart';
import AnalyticsDonutChart from '@/Components/Analytics/AnalyticsDonutChart';
import AnalyticsFiltersBar from '@/Components/Analytics/AnalyticsFiltersBar';
import AnalyticsLineChart from '@/Components/Analytics/AnalyticsLineChart';
import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type ChartPoint = { label: string; value: number };

type Props = PageProps<{
    filters: { academic_year_id: string; date_from: string; date_to: string };
    catalog: { academic_years: SelectOption[] };
    report: {
        type: string;
        title: string;
        summary?: Record<string, number>;
        performance_by_section?: { label: string; average: number }[];
        top_students?: { student: string; code: string; average: number }[];
        risk_students?: { student: string; code: string; average: number }[];
        attendance_trend?: ChartPoint[];
        attendance_distribution?: ChartPoint[];
        most_absences?: { student: string; code: string; absences: number }[];
        income_trend?: ChartPoint[];
        pension_distribution?: ChartPoint[];
        recent_payments?: Record<string, unknown>[];
        top_products?: ChartPoint[];
        sales_trend?: ChartPoint[];
        low_stock?: Record<string, unknown>[];
    };
}>;

export default function AnalyticsReportShow() {
    const { filters, catalog, report } = usePage<Props>().props;
    const type = report.type;

    const exportQuery = Object.fromEntries(Object.entries(filters).filter(([, v]) => v));

    const sectionChart =
        report.performance_by_section?.map((s) => ({ label: s.label, value: s.average })) ?? [];

    return (
        <IntranetLayout title={report.title}>
            <Head title={report.title} />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Analítica', href: route('intranet.analytics.index') },
                        { label: 'Reportes', href: route('intranet.reports.analytics.index') },
                        { label: report.title },
                    ]}
                />
                <SectionTitle
                    title={report.title}
                    actions={
                        <div className="flex gap-2">
                            <a
                                href={route('intranet.reports.analytics.export.pdf', { type, ...exportQuery })}
                                className="rounded-lg border border-plomo/20 px-3 py-2 text-sm font-semibold"
                            >
                                Exportar PDF
                            </a>
                            <a
                                href={route('intranet.reports.analytics.export.csv', { type, ...exportQuery })}
                                className="rounded-lg border border-plomo/20 px-3 py-2 text-sm font-semibold"
                            >
                                Exportar CSV
                            </a>
                            <Link
                                href={route('intranet.reports.analytics.index')}
                                className="rounded-lg bg-navy-900 px-3 py-2 text-sm font-semibold text-white"
                            >
                                Volver
                            </Link>
                        </div>
                    }
                />

                <AnalyticsFiltersBar
                    filters={filters}
                    catalog={catalog}
                    routeName="intranet.reports.analytics.show"
                    routeParams={{ type }}
                />

                {report.summary && (
                    <Card className="mb-6">
                        <h3 className="mb-3 text-sm font-bold text-navy-900">Resumen</h3>
                        <dl className="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                            {Object.entries(report.summary).map(([key, value]) => (
                                <div key={key}>
                                    <dt className="text-xs uppercase text-plomo">{key.replace(/_/g, ' ')}</dt>
                                    <dd className="text-lg font-bold text-navy-900">{value}</dd>
                                </div>
                            ))}
                        </dl>
                    </Card>
                )}

                <div className="grid gap-6 lg:grid-cols-2">
                    {sectionChart.length > 0 && (
                        <AnalyticsBarChart title="Rendimiento por sección" data={sectionChart} />
                    )}
                    {report.attendance_trend && (
                        <AnalyticsLineChart title="Tendencia de asistencia" data={report.attendance_trend} />
                    )}
                    {report.income_trend && (
                        <AnalyticsLineChart title="Ingresos" data={report.income_trend} />
                    )}
                    {report.sales_trend && (
                        <AnalyticsLineChart title="Ventas" data={report.sales_trend} color="#1a2744" />
                    )}
                    {report.attendance_distribution && (
                        <AnalyticsDonutChart
                            title="Distribución de asistencia"
                            data={report.attendance_distribution}
                        />
                    )}
                    {report.pension_distribution && (
                        <AnalyticsDonutChart
                            title="Estado de pensiones"
                            data={report.pension_distribution}
                        />
                    )}
                    {report.top_products && (
                        <AnalyticsBarChart title="Productos más vendidos" data={report.top_products} color="#c41e3a" />
                    )}
                </div>
            </PageContainer>
        </IntranetLayout>
    );
}
