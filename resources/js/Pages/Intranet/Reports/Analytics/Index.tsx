import AnalyticsFiltersBar from '@/Components/Analytics/AnalyticsFiltersBar';
import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { FileBarChart } from 'lucide-react';

type Props = PageProps<{
    filters: { academic_year_id: string; date_from: string; date_to: string };
    catalog: { academic_years: SelectOption[] };
    report_types: { key: string; label: string; description: string }[];
}>;

export default function AnalyticsReportsIndex() {
    const { filters, catalog, report_types } = usePage<Props>().props;

    return (
        <IntranetLayout title="Reportes analíticos">
            <Head title="Reportes analíticos" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Analítica', href: route('intranet.analytics.index') },
                        { label: 'Reportes' },
                    ]}
                />
                <SectionTitle
                    title="Reportes analíticos"
                    description="Exporte indicadores por área en PDF o CSV."
                />

                <AnalyticsFiltersBar
                    filters={filters}
                    catalog={catalog}
                    routeName="intranet.reports.analytics.index"
                />

                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    {report_types.map((report) => (
                        <Card key={report.key} className="flex flex-col">
                            <div className="mb-3 flex items-center gap-2">
                                <FileBarChart className="h-5 w-5 text-navy-900" />
                                <h3 className="font-semibold text-navy-900">{report.label}</h3>
                            </div>
                            <p className="mb-4 flex-1 text-sm text-plomo">{report.description}</p>
                            <div className="flex flex-wrap gap-2">
                                <Link
                                    href={route('intranet.reports.analytics.show', report.key)}
                                    className="rounded-lg bg-navy-900 px-3 py-2 text-sm font-semibold text-white"
                                >
                                    Ver reporte
                                </Link>
                                <a
                                    href={route('intranet.reports.analytics.export.pdf', {
                                        type: report.key,
                                        ...Object.fromEntries(
                                            Object.entries(filters).filter(([, v]) => v),
                                        ),
                                    })}
                                    className="rounded-lg border border-plomo/20 px-3 py-2 text-sm font-semibold"
                                >
                                    PDF
                                </a>
                                <a
                                    href={route('intranet.reports.analytics.export.csv', {
                                        type: report.key,
                                        ...Object.fromEntries(
                                            Object.entries(filters).filter(([, v]) => v),
                                        ),
                                    })}
                                    className="rounded-lg border border-plomo/20 px-3 py-2 text-sm font-semibold"
                                >
                                    CSV
                                </a>
                            </div>
                        </Card>
                    ))}
                </div>
            </PageContainer>
        </IntranetLayout>
    );
}
