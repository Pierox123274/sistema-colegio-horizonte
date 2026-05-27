import AnalyticsBarChart from '@/Components/Analytics/AnalyticsBarChart';
import AnalyticsDonutChart from '@/Components/Analytics/AnalyticsDonutChart';
import AnalyticsFiltersBar from '@/Components/Analytics/AnalyticsFiltersBar';
import AnalyticsKpiCard from '@/Components/Analytics/AnalyticsKpiCard';
import AnalyticsLineChart from '@/Components/Analytics/AnalyticsLineChart';
import RecentAnnouncementsPanel from '@/Components/Announcements/RecentAnnouncementsPanel';
import { AppCard } from '@/Components/App/AppCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { AppTable } from '@/Components/App/AppTable';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import {
    AlertTriangle,
    GraduationCap,
    Package,
    TrendingUp,
    Users,
    Wallet,
} from 'lucide-react';

type ChartPoint = { label: string; value: number };

type Props = PageProps<{
    filters: { academic_year_id: string; date_from: string; date_to: string };
    catalog: { academic_years: SelectOption[] };
    permissions: { financial: boolean; inventory: boolean; users: boolean };
    academic?: {
        summary: Record<string, number>;
        performance_by_section: { label: string; average: number }[];
        top_students: { student: string; code: string; average: number }[];
        risk_students: { student: string; code: string; average: number }[];
        attendance_trend: ChartPoint[];
        attendance_distribution: ChartPoint[];
    };
    financial?: {
        summary: Record<string, number>;
        income_trend: ChartPoint[];
        pension_distribution: ChartPoint[];
        recent_payments: { code: string; student: string; amount: number; paid_at: string }[];
    };
    inventory?: {
        summary: Record<string, number>;
        low_stock: { name: string; code: string; current_stock: number }[];
        top_products: ChartPoint[];
        sales_trend: ChartPoint[];
    };
    users?: Record<string, number>;
    announcements?: { unread_count: number };
}>;

function RankingTable({
    title,
    rows,
    valueLabel,
}: {
    title: string;
    rows: { student: string; code: string; average: number }[];
    valueLabel: string;
}) {
    return (
        <AppCard>
            <h3 className="mb-3 text-sm font-bold text-navy-900">{title}</h3>
            {rows.length === 0 ? (
                <p className="text-sm text-plomo">Sin registros.</p>
            ) : (
                <table className="min-w-full text-sm">
                    <thead>
                        <tr className="border-b border-plomo/15 text-left text-xs uppercase text-plomo">
                            <th className="py-2">Estudiante</th>
                            <th className="py-2">Código</th>
                            <th className="py-2">{valueLabel}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {rows.map((row) => (
                            <tr key={row.code} className="border-b border-plomo/10">
                                <td className="py-2 font-medium">{row.student}</td>
                                <td className="py-2 text-plomo">{row.code}</td>
                                <td className="py-2 font-semibold">{row.average}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            )}
        </AppCard>
    );
}

export default function IntranetAnalyticsIndex() {
    const { filters, catalog, permissions, academic, financial, inventory, users, announcements } =
        usePage<Props>().props;

    const sectionChart =
        academic?.performance_by_section.map((s) => ({ label: s.label, value: s.average })) ?? [];

    return (
        <IntranetLayout title="Analítica institucional">
            <Head title="Dashboard analítico" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Analítica', href: route('intranet.analytics.index') },
                        { label: 'Dashboard ejecutivo' },
                    ]}
                />
                <AppPageHeader
                    title="Dashboard ejecutivo"
                    description="Indicadores académicos, financieros y operativos consolidados."
                    actions={
                        <Link
                            href={route('intranet.reports.analytics.index')}
                            className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900"
                        >
                            Ver reportes exportables
                        </Link>
                    }
                />

                <AnalyticsFiltersBar
                    filters={filters}
                    catalog={catalog}
                    routeName="intranet.analytics.index"
                />

                <RecentAnnouncementsPanel title="Comunicados recientes" />

                {academic && (
                    <section className="mb-10">
                        <h2 className="mb-4 text-lg font-bold text-navy-900">Académico</h2>
                        <div className="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <AnalyticsKpiCard
                                title="Estudiantes matriculados"
                                value={academic.summary.total_students}
                                icon={GraduationCap}
                            />
                            <AnalyticsKpiCard
                                title="Asistencia promedio"
                                value={`${academic.summary.attendance_average}%`}
                                accent="yellow"
                            />
                            <AnalyticsKpiCard
                                title="Promedio institucional"
                                value={academic.summary.institutional_average}
                                icon={TrendingUp}
                            />
                            <AnalyticsKpiCard
                                title="En riesgo académico"
                                value={academic.summary.risk_students_count}
                                icon={AlertTriangle}
                                accent="red"
                            />
                        </div>
                        <div className="mb-6 grid gap-6 lg:grid-cols-2">
                            <AnalyticsBarChart
                                title="Rendimiento por sección"
                                data={sectionChart}
                            />
                            <AnalyticsLineChart
                                title="Tendencia de registros de asistencia"
                                data={academic.attendance_trend}
                            />
                        </div>
                        <div className="grid gap-6 lg:grid-cols-3">
                            <AnalyticsDonutChart
                                title="Distribución de asistencia"
                                data={academic.attendance_distribution}
                            />
                            <RankingTable title="Top estudiantes" rows={academic.top_students} valueLabel="Promedio" />
                            <RankingTable
                                title="Estudiantes en riesgo"
                                rows={academic.risk_students}
                                valueLabel="Promedio"
                            />
                        </div>
                    </section>
                )}

                {permissions.financial && financial && (
                    <section className="mb-10">
                        <h2 className="mb-4 text-lg font-bold text-navy-900">Financiero</h2>
                        <div className="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <AnalyticsKpiCard
                                title="Ingresos (período)"
                                value={`S/ ${financial.summary.total_income}`}
                                icon={Wallet}
                            />
                            <AnalyticsKpiCard
                                title="Pensiones pendientes"
                                value={financial.summary.pending_pensions}
                                accent="yellow"
                            />
                            <AnalyticsKpiCard
                                title="Pensiones pagadas"
                                value={financial.summary.paid_pensions}
                            />
                            <AnalyticsKpiCard
                                title="Morosidad"
                                value={`${financial.summary.morosity_rate}%`}
                                accent="red"
                            />
                        </div>
                        <div className="grid gap-6 lg:grid-cols-2">
                            <AnalyticsLineChart title="Ingresos por día" data={financial.income_trend} />
                            <AnalyticsDonutChart
                                title="Estado de pensiones"
                                data={financial.pension_distribution}
                            />
                        </div>
                        <div className="mt-6">
                        <AppTable title="Pagos recientes">
                            <table className="min-w-full text-sm">
                                <thead>
                                    <tr className="border-b border-plomo/15 text-left text-xs uppercase text-plomo">
                                        <th className="py-2">Código</th>
                                        <th className="py-2">Estudiante</th>
                                        <th className="py-2">Monto</th>
                                        <th className="py-2">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {financial.recent_payments.map((p) => (
                                        <tr key={p.code} className="border-b border-plomo/10">
                                            <td className="py-2">{p.code}</td>
                                            <td className="py-2">{p.student}</td>
                                            <td className="py-2 font-semibold">S/ {p.amount}</td>
                                            <td className="py-2 text-plomo">{p.paid_at}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </AppTable>
                        </div>
                    </section>
                )}

                {permissions.inventory && inventory && (
                    <section className="mb-10">
                        <h2 className="mb-4 text-lg font-bold text-navy-900">Inventario y ventas</h2>
                        <div className="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <AnalyticsKpiCard
                                title="Stock bajo"
                                value={inventory.summary.low_stock_count}
                                icon={Package}
                                accent="red"
                            />
                            <AnalyticsKpiCard
                                title="Ventas hoy"
                                value={inventory.summary.sales_today}
                            />
                            <AnalyticsKpiCard
                                title="Ingresos ventas hoy"
                                value={`S/ ${inventory.summary.sales_income_today}`}
                                accent="yellow"
                            />
                            <AnalyticsKpiCard
                                title="Ingresos período"
                                value={`S/ ${inventory.summary.period_sales_income}`}
                            />
                        </div>
                        <div className="grid gap-6 lg:grid-cols-2">
                            <AnalyticsBarChart
                                title="Productos más vendidos"
                                data={inventory.top_products}
                                color="#c41e3a"
                            />
                            <AnalyticsLineChart title="Ingresos por ventas" data={inventory.sales_trend} />
                        </div>
                    </section>
                )}

                {permissions.users && users && (
                    <section className="mb-10">
                        <h2 className="mb-4 text-lg font-bold text-navy-900">Usuarios y matrículas</h2>
                        <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <AnalyticsKpiCard title="Usuarios activos" value={users.total_users} icon={Users} />
                            <AnalyticsKpiCard title="Docentes activos" value={users.active_teachers} />
                            <AnalyticsKpiCard title="Estudiantes activos" value={users.active_students} />
                            <AnalyticsKpiCard title="Matrículas activas" value={users.active_enrollments} />
                        </div>
                    </section>
                )}

                {announcements && announcements.unread_count > 0 && (
                    <p className="text-sm text-plomo">
                        Comunicados sin leer en su bandeja:{' '}
                        <span className="font-semibold text-navy-900">{announcements.unread_count}</span>
                    </p>
                )}
            </PageContainer>
        </IntranetLayout>
    );
}
