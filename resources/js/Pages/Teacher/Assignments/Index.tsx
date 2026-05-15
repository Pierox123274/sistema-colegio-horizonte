import AssignmentsTabs, { type AssignmentsTabId } from '@/Components/Teacher/Assignments/AssignmentsTabs';
import { RoleBadge } from '@/Components/Teacher/RoleBadge';
import { TeacherAssignmentsEmpty } from '@/Components/Teacher/AssignmentsOverview';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { StatsCard } from '@/Components/Intranet/StatsCard';
import type { SectionOverview } from '@/Components/Teacher/AssignmentsOverview';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import {
    BookMarked,
    CalendarCheck,
    ClipboardCheck,
    GraduationCap,
    Layers,
    Search,
    UserCheck,
    Users,
} from 'lucide-react';
import { useMemo, useState } from 'react';

type Summary = {
    sections_count: number;
    courses_count: number;
    students_count: number;
    tutorias_count: number;
};

type SectionGroup = {
    level: string;
    grades: Array<{
        grade: string;
        sections: SectionOverview[];
    }>;
};

type CourseGroup = {
    subject_id: number;
    subject_name: string;
    items: Array<{
        assignment_id: number;
        section_id: number;
        section_label: string;
        level?: string;
        grade?: string;
        section?: string;
        is_tutor: boolean;
        students_count: number;
        evaluations_count: number;
        links: { grades: string; students: string };
    }>;
};

type StudentRow = {
    id: number;
    code: string;
    first_name: string;
    last_name: string;
    section_id: number;
    section_label: string;
    courses_label: string;
    enrollment_status: string;
    enrollment_status_label: string;
};

type Overview = {
    academic_year: { id: number; name: string; year: number } | null;
    summary: Summary;
    sections: SectionOverview[];
    sections_grouped: SectionGroup[];
    courses_grouped: CourseGroup[];
    students: StudentRow[];
};

type P = PageProps<{
    overview: Overview;
    active_tab: AssignmentsTabId;
    has_teaching_assignments: boolean;
    empty_message: string;
}>;

function SectionActionLinks({ section }: { section: SectionOverview }) {
    return (
        <div className="flex flex-wrap gap-2">
            <Link
                href={section.links.students}
                className="inline-flex items-center gap-1.5 rounded-lg border border-plomo/20 bg-white px-3 py-2 text-xs font-semibold text-navy-900 shadow-sm transition hover:bg-navy-50"
            >
                <Users className="h-3.5 w-3.5" />
                Ver estudiantes
            </Link>
            <Link
                href={section.links.attendance}
                className="inline-flex items-center gap-1.5 rounded-lg border border-plomo/20 bg-white px-3 py-2 text-xs font-semibold text-navy-900 shadow-sm transition hover:bg-navy-50"
            >
                <CalendarCheck className="h-3.5 w-3.5" />
                Registrar asistencia
            </Link>
            <Link
                href={section.links.grades}
                className="inline-flex items-center gap-1.5 rounded-lg bg-navy-900 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-navy-950"
            >
                <ClipboardCheck className="h-3.5 w-3.5" />
                Registrar notas
            </Link>
        </div>
    );
}

export default function TeacherAssignmentsIndex() {
    const { overview, active_tab, has_teaching_assignments, empty_message } = usePage<P>().props;

    const [studentSearch, setStudentSearch] = useState('');
    const [sectionFilter, setSectionFilter] = useState('');

    const filteredStudents = useMemo(() => {
        let rows = overview.students;
        if (sectionFilter) {
            rows = rows.filter((s) => String(s.section_id) === sectionFilter);
        }
        if (studentSearch.trim()) {
            const q = studentSearch.trim().toLowerCase();
            rows = rows.filter(
                (s) =>
                    s.first_name.toLowerCase().includes(q) ||
                    s.last_name.toLowerCase().includes(q) ||
                    s.code.toLowerCase().includes(q),
            );
        }
        return rows;
    }, [overview.students, sectionFilter, studentSearch]);

    const sectionFilterOptions = useMemo(
        () =>
            overview.sections.map((s) => ({
                value: String(s.section_id),
                label: [s.level, s.grade, s.section].filter(Boolean).join(' · '),
            })),
        [overview.sections],
    );

    if (!has_teaching_assignments) {
        return (
            <TeacherLayout title="Mis asignaciones">
                <Head title="Portal docente — Mis asignaciones" />
                <PageContainer>
                    <SectionTitle
                        title="Mis asignaciones académicas"
                        description="Consulte sus secciones, cursos y estudiantes a cargo en el año activo."
                    />
                    <TeacherAssignmentsEmpty message={empty_message} />
                </PageContainer>
            </TeacherLayout>
        );
    }

    return (
        <TeacherLayout title="Mis asignaciones">
            <Head title="Portal docente — Mis asignaciones" />

            <PageContainer>
                <SectionTitle
                    title="Mis asignaciones académicas"
                    description="Organice su carga docente por secciones, cursos y estudiantes del año en curso."
                />

                {overview.academic_year ? (
                    <div className="mb-4 rounded-xl border border-plomo/15 bg-gradient-to-r from-navy-50/80 to-white px-4 py-3 text-sm shadow-sm">
                        <span className="font-semibold text-navy-900">Año académico activo: </span>
                        <span className="text-plomo">
                            {overview.academic_year.name} ({overview.academic_year.year})
                        </span>
                    </div>
                ) : null}

                <AssignmentsTabs active={active_tab} />

                {active_tab === 'resumen' ? (
                    <div className="space-y-6">
                        <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <StatsCard
                                title="Secciones asignadas"
                                value={String(overview.summary.sections_count)}
                                subtitle="Grupos a su cargo"
                                icon={Layers}
                                accent="navy"
                            />
                            <StatsCard
                                title="Cursos asignados"
                                value={String(overview.summary.courses_count)}
                                subtitle="Asignaturas distintas"
                                icon={BookMarked}
                                accent="yellow"
                            />
                            <StatsCard
                                title="Estudiantes a cargo"
                                value={String(overview.summary.students_count)}
                                subtitle="Matriculados en sus secciones"
                                icon={Users}
                                accent="red"
                            />
                            <StatsCard
                                title="Tutorías"
                                value={String(overview.summary.tutorias_count)}
                                subtitle="Secciones como tutor de aula"
                                icon={GraduationCap}
                                accent="navy"
                            />
                        </div>

                        <div className="grid gap-4 lg:grid-cols-2">
                            <Card>
                                <h3 className="mb-3 text-sm font-bold uppercase tracking-wider text-navy-900">
                                    Vista rápida por sección
                                </h3>
                                <ul className="space-y-3">
                                    {overview.sections.slice(0, 4).map((section) => (
                                        <li
                                            key={section.section_id}
                                            className="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-plomo/10 bg-navy-50/30 px-3 py-2.5"
                                        >
                                            <div>
                                                <p className="text-sm font-semibold text-navy-900">
                                                    {section.section} · {section.grade}
                                                </p>
                                                <p className="text-xs text-plomo">
                                                    {section.students_count} estudiantes
                                                </p>
                                            </div>
                                            <RoleBadge isTutor={section.is_tutor} compact />
                                        </li>
                                    ))}
                                </ul>
                            </Card>
                            <Card>
                                <h3 className="mb-3 text-sm font-bold uppercase tracking-wider text-navy-900">
                                    Cursos destacados
                                </h3>
                                <ul className="space-y-3">
                                    {overview.courses_grouped.slice(0, 4).map((course) => (
                                        <li
                                            key={course.subject_id}
                                            className="rounded-lg border border-plomo/10 px-3 py-2.5"
                                        >
                                            <p className="font-semibold text-navy-900">
                                                {course.subject_name}
                                            </p>
                                            <p className="text-xs text-plomo">
                                                {course.items.length} sección
                                                {course.items.length === 1 ? '' : 'es'}
                                            </p>
                                        </li>
                                    ))}
                                </ul>
                            </Card>
                        </div>
                    </div>
                ) : null}

                {active_tab === 'secciones' ? (
                    <div className="space-y-8">
                        {overview.sections_grouped.map((levelGroup) => (
                            <section key={levelGroup.level}>
                                <h2 className="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-navy-900">
                                    <Layers className="h-4 w-4" />
                                    {levelGroup.level}
                                </h2>
                                <div className="space-y-6">
                                    {levelGroup.grades.map((gradeGroup) => (
                                        <div key={`${levelGroup.level}-${gradeGroup.grade}`}>
                                            <p className="mb-3 text-xs font-semibold uppercase text-plomo">
                                                Grado {gradeGroup.grade}
                                            </p>
                                            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                                {gradeGroup.sections.map((section) => (
                                                    <Card
                                                        key={section.section_id}
                                                        className="flex flex-col border-l-4 border-l-navy-900/20"
                                                    >
                                                        <div className="mb-3 flex items-start justify-between gap-2">
                                                            <div>
                                                                <p className="text-xs text-plomo">
                                                                    Sección {section.section}
                                                                </p>
                                                                <h3 className="text-lg font-bold text-navy-900">
                                                                    {section.grade}
                                                                </h3>
                                                                <p className="text-xs text-plomo">
                                                                    {section.level}
                                                                </p>
                                                            </div>
                                                            <RoleBadge isTutor={section.is_tutor} />
                                                        </div>

                                                        {section.courses.length > 0 ? (
                                                            <div className="mb-3 flex flex-wrap gap-1.5">
                                                                {section.courses.map((c) => (
                                                                    <span
                                                                        key={c.id}
                                                                        className="rounded-md bg-white px-2 py-0.5 text-xs font-medium text-navy-900 ring-1 ring-plomo/15"
                                                                    >
                                                                        {c.name}
                                                                    </span>
                                                                ))}
                                                            </div>
                                                        ) : (
                                                            <p className="mb-3 text-xs text-plomo">
                                                                Sin curso específico en esta asignación
                                                            </p>
                                                        )}

                                                        <p className="mb-4 flex items-center gap-1.5 text-sm text-plomo">
                                                            <Users className="h-4 w-4" />
                                                            {section.students_count} estudiantes
                                                            matriculados
                                                        </p>

                                                        <div className="mt-auto border-t border-plomo/10 pt-4">
                                                            <SectionActionLinks section={section} />
                                                        </div>
                                                    </Card>
                                                ))}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </section>
                        ))}
                    </div>
                ) : null}

                {active_tab === 'cursos' ? (
                    <div className="space-y-6">
                        {overview.courses_grouped.map((course) => (
                            <Card key={course.subject_id} className="overflow-hidden p-0">
                                <div className="border-b border-plomo/10 bg-navy-50/50 px-5 py-4">
                                    <div className="flex flex-wrap items-center justify-between gap-2">
                                        <div className="flex items-center gap-2">
                                            <BookMarked className="h-5 w-5 text-navy-900" />
                                            <h3 className="text-base font-bold text-navy-900">
                                                {course.subject_name}
                                            </h3>
                                        </div>
                                        <span className="rounded-full bg-white px-2.5 py-0.5 text-xs font-semibold text-plomo ring-1 ring-plomo/15">
                                            {course.items.length} asignación
                                            {course.items.length === 1 ? '' : 'es'}
                                        </span>
                                    </div>
                                </div>
                                <ul className="divide-y divide-plomo/10">
                                    {course.items.map((item) => (
                                        <li
                                            key={item.assignment_id}
                                            className="flex flex-col gap-4 px-5 py-4 lg:flex-row lg:items-center lg:justify-between"
                                        >
                                            <div className="min-w-0 flex-1">
                                                <p className="font-semibold text-navy-900">
                                                    {item.section_label}
                                                </p>
                                                <div className="mt-2 flex flex-wrap items-center gap-2 text-xs text-plomo">
                                                    <RoleBadge isTutor={item.is_tutor} compact />
                                                    <span>
                                                        {item.students_count} estudiantes
                                                    </span>
                                                    <span>·</span>
                                                    <span>
                                                        {item.evaluations_count} evaluación
                                                        {item.evaluations_count === 1 ? '' : 'es'}
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex shrink-0 flex-wrap gap-2">
                                                <Link
                                                    href={item.links.students}
                                                    className="rounded-lg border border-plomo/20 px-3 py-1.5 text-xs font-semibold text-navy-900 hover:bg-navy-50"
                                                >
                                                    Estudiantes
                                                </Link>
                                                <Link
                                                    href={item.links.grades}
                                                    className="rounded-lg bg-navy-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-navy-950"
                                                >
                                                    Registrar notas
                                                </Link>
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            </Card>
                        ))}
                    </div>
                ) : null}

                {active_tab === 'estudiantes' ? (
                    <div className="space-y-4">
                        <Card>
                            <div className="grid gap-4 md:grid-cols-[1fr_200px_auto] md:items-end">
                                <div>
                                    <label className="block text-xs font-semibold uppercase text-plomo">
                                        Buscar estudiante
                                    </label>
                                    <div className="relative mt-1">
                                        <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-plomo" />
                                        <input
                                            type="search"
                                            value={studentSearch}
                                            onChange={(e) => setStudentSearch(e.target.value)}
                                            placeholder="Nombre o código"
                                            className="w-full rounded-lg border border-plomo/20 py-2 pl-9 pr-3 text-sm"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-xs font-semibold uppercase text-plomo">
                                        Sección
                                    </label>
                                    <select
                                        value={sectionFilter}
                                        onChange={(e) => setSectionFilter(e.target.value)}
                                        className="mt-1 w-full rounded-lg border border-plomo/20 px-3 py-2 text-sm"
                                    >
                                        <option value="">Todas mis secciones</option>
                                        {sectionFilterOptions.map((o) => (
                                            <option key={o.value} value={o.value}>
                                                {o.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <p className="text-sm text-plomo md:pb-2">
                                    {filteredStudents.length} resultado
                                    {filteredStudents.length === 1 ? '' : 's'}
                                </p>
                            </div>
                        </Card>

                        <Card className="overflow-hidden p-0">
                            <div className="overflow-x-auto">
                                <table className="min-w-full text-left text-sm">
                                    <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo">
                                        <tr>
                                            <th className="px-4 py-3">Estudiante</th>
                                            <th className="px-4 py-3">Código</th>
                                            <th className="px-4 py-3">Sección</th>
                                            <th className="px-4 py-3">Curso(s)</th>
                                            <th className="px-4 py-3">Matrícula</th>
                                            <th className="px-4 py-3 text-right">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-plomo/10">
                                        {filteredStudents.map((student) => (
                                            <tr
                                                key={student.id}
                                                className="bg-white hover:bg-navy-50/40"
                                            >
                                                <td className="px-4 py-3 font-medium text-navy-900">
                                                    {student.last_name}, {student.first_name}
                                                </td>
                                                <td className="px-4 py-3 font-mono text-xs">
                                                    {student.code}
                                                </td>
                                                <td className="px-4 py-3 text-xs text-plomo">
                                                    {student.section_label}
                                                </td>
                                                <td className="px-4 py-3 text-xs">
                                                    {student.courses_label}
                                                </td>
                                                <td className="px-4 py-3">
                                                    <span className="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-200">
                                                        {student.enrollment_status_label}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 text-right">
                                                    <Link
                                                        href={route(
                                                            'teacher.students.show',
                                                            student.id,
                                                        )}
                                                        className="inline-flex items-center gap-1 text-sm font-semibold text-navy-900 hover:underline"
                                                    >
                                                        <UserCheck className="h-4 w-4" />
                                                        Ver ficha
                                                    </Link>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            {filteredStudents.length === 0 ? (
                                <p className="px-4 py-8 text-center text-sm text-plomo">
                                    No hay estudiantes con los filtros aplicados.
                                </p>
                            ) : null}
                        </Card>
                    </div>
                ) : null}
            </PageContainer>
        </TeacherLayout>
    );
}
