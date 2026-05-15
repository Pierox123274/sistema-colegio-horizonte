import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { FileSpreadsheet, FileText } from 'lucide-react';

type P = PageProps<{
    links: {
        attendance_reports: string;
        attendance_pdf: string;
        attendance_excel: string;
        grades_reports: string;
        grades_pdf: string;
        grades_excel: string;
    };
}>;

export default function TeacherReportsIndex() {
    const { links } = usePage<P>().props;

    return (
        <TeacherLayout title="Reportes">
            <Head title="Portal docente — Reportes" />

            <PageContainer>
                <SectionTitle
                    title="Reportes académicos"
                    description="Las exportaciones reutilizan las rutas y plantillas del ERP (PDF y CSV), con las mismas políticas de acceso."
                />

                <div className="grid gap-6 md:grid-cols-2">
                    <Card>
                        <h2 className="mb-2 text-sm font-bold uppercase tracking-wider text-navy-900">
                            Asistencia
                        </h2>
                        <p className="mb-4 text-sm text-plomo">
                            Filtros y tablas completas en el módulo de reportes
                            del ERP. Aquí tiene accesos directos a exportación.
                        </p>
                        <div className="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                            <Link
                                href={links.attendance_reports}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                            >
                                <FileText className="h-4 w-4" />
                                Pantalla de reportes
                            </Link>
                            <a
                                href={links.attendance_pdf}
                                className="inline-flex items-center justify-center gap-2 rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900"
                            >
                                PDF (filtros en ERP)
                            </a>
                            <a
                                href={links.attendance_excel}
                                className="inline-flex items-center justify-center gap-2 rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900"
                            >
                                <FileSpreadsheet className="h-4 w-4" />
                                CSV
                            </a>
                        </div>
                    </Card>

                    <Card>
                        <h2 className="mb-2 text-sm font-bold uppercase tracking-wider text-navy-900">
                            Notas
                        </h2>
                        <p className="mb-4 text-sm text-plomo">
                            Promedios y riesgo académico en el ERP. Use los
                            enlaces para exportar con los filtros aplicados
                            allí.
                        </p>
                        <div className="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                            <Link
                                href={links.grades_reports}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                            >
                                <FileText className="h-4 w-4" />
                                Pantalla de reportes
                            </Link>
                            <a
                                href={links.grades_pdf}
                                className="inline-flex items-center justify-center gap-2 rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900"
                            >
                                PDF
                            </a>
                            <a
                                href={links.grades_excel}
                                className="inline-flex items-center justify-center gap-2 rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900"
                            >
                                <FileSpreadsheet className="h-4 w-4" />
                                CSV
                            </a>
                        </div>
                    </Card>
                </div>
            </PageContainer>
        </TeacherLayout>
    );
}
