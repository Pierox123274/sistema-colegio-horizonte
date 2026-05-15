import { RoleBadge } from '@/Components/Teacher/RoleBadge';
import { Card } from '@/Components/Intranet/Card';
import { Link } from '@inertiajs/react';
import {
    BookMarked,
    CalendarCheck,
    ClipboardCheck,
    GraduationCap,
    Users,
} from 'lucide-react';

export type AssignmentTableRow = {
    id: number;
    academic_year?: string;
    level?: string;
    grade?: string;
    section?: string;
    subject?: string;
    is_tutor: boolean;
    role_label?: string;
    students_count: number;
};

export type SectionOverview = {
    section_id: number;
    academic_year?: string;
    level?: string;
    grade?: string;
    section?: string;
    is_tutor: boolean;
    role_label?: string;
    courses: Array<{ id: number; name: string }>;
    students_count: number;
    students: Array<{
        id: number;
        code: string;
        first_name: string;
        last_name: string;
    }>;
    links: {
        students: string;
        attendance: string;
        grades: string;
    };
};

type Props = {
    sections: SectionOverview[];
    assignments: AssignmentTableRow[];
    emptyMessage: string;
    compact?: boolean;
    showActions?: boolean;
};

export function TeacherAssignmentsEmpty({ message }: { message: string }) {
    return (
        <div className="rounded-xl border border-dashed border-plomo/25 bg-navy-50/30 px-6 py-10 text-center">
            <GraduationCap className="mx-auto mb-3 h-10 w-10 text-plomo" strokeWidth={1.5} />
            <p className="text-sm font-medium text-navy-900">{message}</p>
        </div>
    );
}

export default function AssignmentsOverview({
    sections,
    assignments,
    emptyMessage,
    compact = false,
    showActions = true,
}: Props) {
    if (sections.length === 0 && assignments.length === 0) {
        return <TeacherAssignmentsEmpty message={emptyMessage} />;
    }

    return (
        <div className="space-y-6">
            {!compact ? (
                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    {sections.map((section) => (
                        <Card key={section.section_id} className="flex flex-col">
                            <div className="mb-3 flex items-start justify-between gap-2">
                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-wide text-plomo">
                                        {section.academic_year}
                                    </p>
                                    <h3 className="mt-1 text-base font-bold text-navy-900">
                                        {section.level} · {section.grade} · {section.section}
                                    </h3>
                                </div>
                                <RoleBadge isTutor={section.is_tutor} />
                            </div>

                            {section.courses.length > 0 ? (
                                <div className="mb-3">
                                    <p className="text-xs font-semibold uppercase text-plomo">Cursos</p>
                                    <ul className="mt-1 flex flex-wrap gap-1.5">
                                        {section.courses.map((course) => (
                                            <li
                                                key={course.id}
                                                className="rounded-md bg-white px-2 py-0.5 text-xs font-medium text-navy-900 ring-1 ring-plomo/15"
                                            >
                                                {course.name}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            ) : (
                                <p className="mb-3 text-xs text-plomo">Sin curso específico asignado</p>
                            )}

                            <p className="mb-4 text-sm text-plomo">
                                <Users className="mr-1 inline h-4 w-4 align-text-bottom" />
                                {section.students_count} estudiante
                                {section.students_count === 1 ? '' : 's'} matriculado
                                {section.students_count === 1 ? '' : 's'}
                            </p>

                            {showActions ? (
                                <div className="mt-auto flex flex-wrap gap-2 border-t border-plomo/10 pt-4">
                                    <Link
                                        href={section.links.students}
                                        className="inline-flex items-center gap-1 rounded-lg border border-plomo/20 px-3 py-1.5 text-xs font-semibold text-navy-900 hover:bg-navy-50"
                                    >
                                        <Users className="h-3.5 w-3.5" />
                                        Ver estudiantes
                                    </Link>
                                    <Link
                                        href={section.links.attendance}
                                        className="inline-flex items-center gap-1 rounded-lg border border-plomo/20 px-3 py-1.5 text-xs font-semibold text-navy-900 hover:bg-navy-50"
                                    >
                                        <CalendarCheck className="h-3.5 w-3.5" />
                                        Registrar asistencia
                                    </Link>
                                    <Link
                                        href={section.links.grades}
                                        className="inline-flex items-center gap-1 rounded-lg bg-navy-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-navy-950"
                                    >
                                        <ClipboardCheck className="h-3.5 w-3.5" />
                                        Registrar notas
                                    </Link>
                                </div>
                            ) : null}
                        </Card>
                    ))}
                </div>
            ) : null}

            {assignments.length > 0 ? (
                <Card>
                    <div className="mb-3 flex items-center gap-2">
                        <BookMarked className="h-5 w-5 text-navy-900" />
                        <h3 className="text-sm font-bold uppercase tracking-wider text-navy-900">
                            Detalle por asignación
                        </h3>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-left text-sm">
                            <thead className="border-b border-plomo/10 text-xs uppercase text-plomo">
                                <tr>
                                    <th className="py-2 pr-3">Año</th>
                                    <th className="py-2 pr-3">Nivel</th>
                                    <th className="py-2 pr-3">Grado</th>
                                    <th className="py-2 pr-3">Sección</th>
                                    <th className="py-2 pr-3">Curso</th>
                                    <th className="py-2 pr-3">Rol</th>
                                    <th className="py-2 text-right">Estudiantes</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-plomo/10">
                                {assignments.map((row) => (
                                    <tr key={row.id}>
                                        <td className="py-2 pr-3">{row.academic_year ?? '—'}</td>
                                        <td className="py-2 pr-3">{row.level ?? '—'}</td>
                                        <td className="py-2 pr-3">{row.grade ?? '—'}</td>
                                        <td className="py-2 pr-3 font-medium text-navy-900">
                                            {row.section ?? '—'}
                                        </td>
                                        <td className="py-2 pr-3 text-plomo">{row.subject ?? '—'}</td>
                                        <td className="py-2 pr-3 text-xs">{row.role_label ?? '—'}</td>
                                        <td className="py-2 text-right font-semibold text-navy-900">
                                            {row.students_count}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </Card>
            ) : null}
        </div>
    );
}
