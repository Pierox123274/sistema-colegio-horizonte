import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { FileSpreadsheet, FileText } from 'lucide-react';

type P = PageProps<{
    links: {
        attendance_pdf: string;
        attendance_excel: string;
        grades_pdf: string;
        grades_excel: string;
    };
    has_teaching_assignments?: boolean;
    teacher_portal_scoped?: boolean;
}>;

export default function TeacherReportsIndex() {
    const { links, has_teaching_assignments, teacher_portal_scoped } = usePage<P>().props;

    return (
        <TeacherLayout title="Reportes">
            <Head title="Portal docente — Reportes" />

            <PageContainer>
                {teacher_portal_scoped && has_teaching_assignments === false ? (
                    <div className="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        Sin secciones asignadas: las exportaciones estarán vacías hasta que tenga
                        carga docente en el año activo.
                    </div>
                ) : null}

                <SectionTitle
                    title="Reportes académicos"
                    description="Descargue asistencia y notas de sus secciones en PDF o CSV. Los archivos respetan el alcance de sus asignaciones."
                />

                <div className="grid gap-6 md:grid-cols-2">
                    <Card>
                        <h2 className="mb-2 text-sm font-bold uppercase tracking-wider text-navy-900">
                            Asistencia
                        </h2>
                        <p className="mb-4 text-sm text-plomo">
                            Exportación del historial de asistencia. Si tiene varias secciones, el
                            archivo usa la primera sección asignada; ajuste filtros en la URL si lo
                            necesita.
                        </p>
                        <div className="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                            <a
                                href={links.attendance_pdf}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                            >
                                <FileText className="h-4 w-4" />
                                Descargar PDF
                            </a>
                            <a
                                href={links.attendance_excel}
                                className="inline-flex items-center justify-center gap-2 rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900"
                            >
                                <FileSpreadsheet className="h-4 w-4" />
                                Descargar CSV
                            </a>
                        </div>
                    </Card>

                    <Card>
                        <h2 className="mb-2 text-sm font-bold uppercase tracking-wider text-navy-900">
                            Notas
                        </h2>
                        <p className="mb-4 text-sm text-plomo">
                            Exportación de calificaciones por sección y evaluación. Use los mismos
                            filtros que en el registro de notas al compartir el enlace.
                        </p>
                        <div className="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                            <a
                                href={links.grades_pdf}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                            >
                                <FileText className="h-4 w-4" />
                                Descargar PDF
                            </a>
                            <a
                                href={links.grades_excel}
                                className="inline-flex items-center justify-center gap-2 rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900"
                            >
                                <FileSpreadsheet className="h-4 w-4" />
                                Descargar CSV
                            </a>
                        </div>
                    </Card>
                </div>
            </PageContainer>
        </TeacherLayout>
    );
}
