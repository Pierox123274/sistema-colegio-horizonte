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

type AssignmentRow = {
    id: number;
    section?: string;
    grade?: string;
    level?: string;
    subject?: string;
    is_tutor: boolean;
};

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
    assignments: AssignmentRow[];
    has_teaching_assignments: boolean;
    teacher_portal_scoped: boolean;
    links: {
        attendance: string;
        attendance_register: string;
        grades: string;
        grades_reports: string;
    };
}>;

export default function TeacherDashboard() {
    const {
        academic_year,
        stats,
        assignments,
        has_teaching_assignments,
        teacher_portal_scoped,
        links,
    } = usePage<DashboardProps>().props;

    const scopeNote = teacher_portal_scoped
        ? 'Cifras y enlaces filtrados por sus secciones asignadas en el año activo.'
        : 'Accesos rápidos a asistencia, notas y estudiantes. El registro detallado se realiza en las herramientas del panel ERP vinculadas aquí.';

    return (
        <TeacherLayout title="Inicio docente">
            <Head title="Portal docente — Inicio" />

            <PageContainer>
                <SectionTitle title="Resumen académico" description={scopeNote} />

                {teacher_portal_scoped && !has_teaching_assignments ? (
                    <div className="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        No tiene secciones docentes asignadas para el año académico
                        activo. Solicite a administración una asignación en{' '}
                        <span className="font-semibold">Administración → Asignaciones docentes</span>.
                    </div>
                ) : null}

                {academic_year ? (
                    <div className="mb-6 rounded-xl border border-plomo/15 bg-white px-4 py-3 text-sm shadow-sm">
                        <span className="font-semibold text-navy-900">
                            Año académico activo:{' '}
                        </span>
                        <span className="text-plomo">
                            {academic_year.name} ({academic_year.year})
                        </span>
                    </div>
                ) : (
                    <div className="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        No hay año académico marcado como activo. Coordine con
                        administración para configurar el año en curso.
                    </div>
                )}

                <div className="mb-10 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <StatsCard
                        title="Estudiantes matriculados"
                        value={String(stats.enrolled_students)}
                        subtitle={
                            teacher_portal_scoped
                                ? 'Matrículas en sus secciones (año activo)'
                                : 'Matrículas activas del año vigente'
                        }
                        icon={Users}
                        accent="navy"
                    />
                    <StatsCard
                        title="Registros de asistencia (7 días)"
                        value={String(stats.attendance_records_week)}
                        subtitle={
                            teacher_portal_scoped
                                ? 'En sus secciones asignadas'
                                : 'Incluye todos los estados registrados'
                        }
                        icon={CalendarCheck}
                        accent="yellow"
                    />
                    <StatsCard
                        title="Cursos / evaluaciones"
                        value={String(stats.subjects_count)}
                        subtitle={
                            teacher_portal_scoped
                                ? 'Cursos vinculados a sus secciones o asignaciones'
                                : 'Asignaturas en el sistema'
                        }
                        icon={BookMarked}
                        accent="red"
                    />
                    <StatsCard
                        title="Evaluaciones / notas"
                        value={`${stats.evaluations_count} / ${stats.grade_records_count}`}
                        subtitle={
                            teacher_portal_scoped
                                ? 'En secciones asignadas'
                                : 'Evaluaciones y registros de calificación'
                        }
                        icon={ClipboardCheck}
                        accent="navy"
                    />
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <div className="mb-4 flex items-center gap-2">
                            <GraduationCap
                                className="h-5 w-5 text-navy-900"
                                strokeWidth={1.75}
                            />
                            <h2 className="text-sm font-bold uppercase tracking-wider text-navy-900">
                                Accesos rápidos
                            </h2>
                        </div>
                        <ul className="space-y-2">
                            {[
                                {
                                    href: route('teacher.attendance.index'),
                                    label: 'Asistencia (portal)',
                                    desc: teacher_portal_scoped
                                        ? 'Listado filtrado a su alumnado'
                                        : 'Historial y enlaces al registro masivo',
                                },
                                {
                                    href: route('teacher.grades.index'),
                                    label: 'Notas (portal)',
                                    desc: teacher_portal_scoped
                                        ? 'Últimas notas en sus secciones'
                                        : 'Últimas calificaciones y registro en ERP',
                                },
                                {
                                    href: route('teacher.students.index'),
                                    label: 'Estudiantes',
                                    desc: teacher_portal_scoped
                                        ? 'Solo matriculados en sus secciones'
                                        : 'Listado filtrable del alumnado',
                                },
                                {
                                    href: route('teacher.reports.index'),
                                    label: 'Reportes',
                                    desc: 'Exportaciones PDF y CSV del ERP',
                                },
                            ].map((item) => (
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
                                        <ArrowRight
                                            className="h-4 w-4 shrink-0 text-plomo"
                                            aria-hidden
                                        />
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </Card>

                    <Card>
                        <h2 className="mb-3 text-sm font-bold uppercase tracking-wider text-navy-900">
                            Registro académico (ERP)
                        </h2>
                        <p className="mb-4 text-sm text-plomo">
                            Enlaces al registro masivo y reportes del ERP. Si tiene
                            secciones asignadas, se abren con filtro a su primera
                            sección del año activo.
                        </p>
                        <div className="flex flex-wrap gap-2">
                            <Link
                                href={links.attendance_register}
                                className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                            >
                                Registrar asistencia
                            </Link>
                            <Link
                                href={links.attendance}
                                className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900 transition hover:bg-navy-50"
                            >
                                Reporte asistencia
                            </Link>
                            <Link
                                href={links.grades}
                                className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900 transition hover:bg-navy-50"
                            >
                                Registro de notas
                            </Link>
                            <Link
                                href={links.grades_reports}
                                className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900 transition hover:bg-navy-50"
                            >
                                Reportes académicos
                            </Link>
                        </div>
                    </Card>
                </div>

                {assignments.length > 0 ? (
                    <Card className="mt-6">
                        <h2 className="mb-3 text-sm font-bold uppercase tracking-wider text-navy-900">
                            Mis asignaciones (año activo)
                        </h2>
                        <div className="overflow-x-auto">
                            <table className="min-w-full text-left text-sm">
                                <thead className="border-b border-plomo/10 text-xs uppercase text-plomo">
                                    <tr>
                                        <th className="py-2 pr-4">Nivel / grado</th>
                                        <th className="py-2 pr-4">Sección</th>
                                        <th className="py-2 pr-4">Curso</th>
                                        <th className="py-2">Tutor</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-plomo/10">
                                    {assignments.map((a) => (
                                        <tr key={a.id}>
                                            <td className="py-2 pr-4 text-navy-900">
                                                {[a.level, a.grade].filter(Boolean).join(' · ')}
                                            </td>
                                            <td className="py-2 pr-4">{a.section ?? '—'}</td>
                                            <td className="py-2 pr-4 text-plomo">
                                                {a.subject ?? '—'}
                                            </td>
                                            <td className="py-2">{a.is_tutor ? 'Sí' : 'No'}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </Card>
                ) : null}
            </PageContainer>
        </TeacherLayout>
    );
}
