import AssignmentsOverview, {
    type AssignmentTableRow,
    type SectionOverview,
    TeacherAssignmentsEmpty,
} from '@/Components/Teacher/AssignmentsOverview';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { StatsCard } from '@/Components/Intranet/StatsCard';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import {
    ArrowRight,
    BookMarked,
    CalendarCheck,
    ClipboardCheck,
    GraduationCap,
    Users,
} from 'lucide-react';

type DashboardProps = PageProps<{
    academic_year: {
        id: number;
        name: string;
        year: number;
        is_active: boolean;
    } | null;
    stats: {
        enrolled_students: number;
        attendance_records_week: number;
        subjects_count: number;
        evaluations_count: number;
        grade_records_count: number;
    };
    assignments: AssignmentTableRow[];
    assignments_overview: {
        academic_year: { id: number; name: string; year: number } | null;
        sections: SectionOverview[];
        course_assignments: unknown[];
    };
    has_teaching_assignments: boolean;
    teacher_portal_scoped: boolean;
    empty_message: string;
    links: {
        assignments: string;
        attendance: string;
        attendance_register: string;
        grades: string;
        grades_summary: string;
        students: string;
        reports: string;
    };
}>;

export default function TeacherDashboard() {
    const {
        academic_year,
        stats,
        assignments,
        assignments_overview,
        has_teaching_assignments,
        teacher_portal_scoped,
        empty_message,
        links,
    } = usePage<DashboardProps>().props;

    const scopeNote = teacher_portal_scoped
        ? 'Resumen de su registro académico en el año activo, limitado a las secciones que tiene asignadas.'
        : 'Resumen del registro académico y accesos a las herramientas del portal docente.';

    const tools = [
        { href: links.assignments, label: 'Mis asignaciones', desc: 'Secciones, cursos y alumnos a su cargo' },
        { href: links.attendance_register, label: 'Registrar asistencia', desc: 'Marcar asistencia por fecha y sección' },
        { href: links.attendance, label: 'Consultar asistencia', desc: 'Últimos registros de sus secciones' },
        { href: links.grades, label: 'Registro de notas', desc: 'Cargar calificaciones por evaluación' },
        { href: links.students, label: 'Mis estudiantes', desc: 'Alumnado de sus secciones asignadas' },
        { href: links.reports, label: 'Reportes', desc: 'Exportar asistencia y notas en PDF o CSV' },
    ];

    return (
        <TeacherLayout title="Inicio docente">
            <Head title="Portal docente — Inicio" />

            <PageContainer>
                <SectionTitle title="Registro académico" description={scopeNote} />

                {academic_year ? (
                    <div className="mb-6 rounded-xl border border-plomo/15 bg-white px-4 py-3 text-sm shadow-sm">
                        <span className="font-semibold text-navy-900">Año académico activo: </span>
                        <span className="text-plomo">
                            {academic_year.name} ({academic_year.year})
                        </span>
                    </div>
                ) : (
                    <div className="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        No hay año académico marcado como activo. Coordine con administración
                        para configurar el año en curso.
                    </div>
                )}

                <div className="mb-10 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <StatsCard
                        title="Estudiantes matriculados"
                        value={String(stats.enrolled_students)}
                        subtitle={teacher_portal_scoped ? 'En sus secciones' : 'Matrículas activas'}
                        icon={Users}
                        accent="navy"
                    />
                    <StatsCard
                        title="Asistencia (7 días)"
                        value={String(stats.attendance_records_week)}
                        subtitle={teacher_portal_scoped ? 'En sus secciones' : 'Registros recientes'}
                        icon={CalendarCheck}
                        accent="yellow"
                    />
                    <StatsCard
                        title="Cursos asignados"
                        value={String(stats.subjects_count)}
                        subtitle={teacher_portal_scoped ? 'Según sus asignaciones' : 'En el sistema'}
                        icon={BookMarked}
                        accent="red"
                    />
                    <StatsCard
                        title="Evaluaciones / notas"
                        value={`${stats.evaluations_count} / ${stats.grade_records_count}`}
                        subtitle={teacher_portal_scoped ? 'En sus secciones' : 'Totales'}
                        icon={ClipboardCheck}
                        accent="navy"
                    />
                </div>

                <Card className="mb-8">
                    <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <div className="flex items-center gap-2">
                            <GraduationCap className="h-5 w-5 text-navy-900" strokeWidth={1.75} />
                            <h2 className="text-sm font-bold uppercase tracking-wider text-navy-900">
                                Mis asignaciones
                            </h2>
                        </div>
                        {has_teaching_assignments ? (
                            <Link
                                href={links.assignments}
                                className="inline-flex items-center gap-1 text-sm font-semibold text-navy-900 hover:underline"
                            >
                                Ver detalle completo
                                <ArrowRight className="h-4 w-4" />
                            </Link>
                        ) : null}
                    </div>

                    {teacher_portal_scoped && !has_teaching_assignments ? (
                        <TeacherAssignmentsEmpty message={empty_message} />
                    ) : (
                        <AssignmentsOverview
                            sections={assignments_overview.sections}
                            assignments={assignments}
                            emptyMessage={empty_message}
                            compact={false}
                            showActions
                        />
                    )}
                </Card>

                <Card>
                    <div className="mb-4 flex items-center gap-2">
                        <GraduationCap className="h-5 w-5 text-navy-900" strokeWidth={1.75} />
                        <h2 className="text-sm font-bold uppercase tracking-wider text-navy-900">
                            Herramientas del docente
                        </h2>
                    </div>
                    <ul className="space-y-2">
                        {tools.map((item) => (
                            <li key={item.href}>
                                <Link
                                    href={item.href}
                                    className="flex items-center justify-between gap-3 rounded-lg border border-plomo/15 bg-navy-50/40 px-4 py-3 text-sm font-semibold text-navy-900 transition hover:border-navy-900/25 hover:bg-white"
                                >
                                    <span>
                                        {item.label}
                                        <span className="mt-0.5 block text-xs font-normal text-plomo">
                                            {item.desc}
                                        </span>
                                    </span>
                                    <ArrowRight className="h-4 w-4 shrink-0 text-plomo" aria-hidden />
                                </Link>
                            </li>
                        ))}
                    </ul>
                </Card>
            </PageContainer>
        </TeacherLayout>
    );
}
