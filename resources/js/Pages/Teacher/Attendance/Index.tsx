import { TeacherAssignmentsEmpty } from '@/Components/Teacher/AssignmentsOverview';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { ClipboardCheck, FileDown, UserCheck } from 'lucide-react';
import { FormEvent, useState } from 'react';

type Row = {
    id: number;
    attendance_date: string;
    status: 'presente' | 'tarde' | 'falta' | 'justificado';
    observation: string | null;
    student?: {
        id: number;
        code: string;
        first_name: string;
        last_name: string;
    };
    section?: { name: string };
};

type P = PageProps<{
    filters: { student_id: string; section_id: string };
    catalog: { students: SelectOption[]; sections: SelectOption[] };
    recent_attendances: Row[];
    links: { register: string; index: string; reports: string };
    has_teaching_assignments?: boolean;
    teacher_portal_scoped?: boolean;
    empty_message?: string;
}>;

export default function TeacherAttendanceIndex() {
    const {
        filters,
        catalog,
        recent_attendances,
        links,
        has_teaching_assignments,
        teacher_portal_scoped,
        empty_message,
    } = usePage<P>().props;
    const [studentId, setStudentId] = useState(filters.student_id || '');
    const [sectionId, setSectionId] = useState(filters.section_id || '');

    const goToStudent = (e?: FormEvent) => {
        e?.preventDefault();
        if (!studentId) return;
        router.visit(route('teacher.students.show', studentId));
    };

    const applySectionFilter = (value: string) => {
        setSectionId(value);
        router.get(
            route('teacher.attendance.index'),
            {
                section_id: value || undefined,
                student_id: studentId || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const registerHref =
        sectionId !== ''
            ? `${links.register}?section_id=${sectionId}`
            : links.register;

    return (
        <TeacherLayout title="Asistencia">
            <Head title="Portal docente — Asistencia" />

            <PageContainer>
                {teacher_portal_scoped && has_teaching_assignments === false ? (
                    <div className="mb-6">
                        <TeacherAssignmentsEmpty
                            message={
                                empty_message ??
                                'Aún no tienes secciones o cursos asignados. Contacta al administrador académico.'
                            }
                        />
                    </div>
                ) : null}

                <SectionTitle
                    title="Asistencia"
                    description="Consulte y registre asistencia solo en las secciones que tiene asignadas."
                    actions={
                        <div className="flex flex-wrap gap-2">
                            <Link
                                href={registerHref}
                                className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                            >
                                <ClipboardCheck className="h-4 w-4" />
                                Registrar asistencia
                            </Link>
                            <Link
                                href={links.reports}
                                className="inline-flex items-center gap-2 rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900"
                            >
                                <FileDown className="h-4 w-4" />
                                Exportar reportes
                            </Link>
                        </div>
                    }
                />

                {catalog.sections.length > 0 ? (
                    <Card className="mb-6">
                        <label
                            htmlFor="section_filter"
                            className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                        >
                            Filtrar por sección asignada
                        </label>
                        <select
                            id="section_filter"
                            value={sectionId}
                            onChange={(e) => applySectionFilter(e.target.value)}
                            className="mt-1 w-full max-w-md rounded-md border border-plomo/20 px-3 py-2 text-sm"
                        >
                            <option value="">Todas mis secciones</option>
                            {catalog.sections.map((s) => (
                                <option key={s.value} value={s.value}>
                                    {s.label}
                                </option>
                            ))}
                        </select>
                    </Card>
                ) : null}

                <Card className="mb-6">
                    <form onSubmit={goToStudent} className="grid gap-4 md:grid-cols-[1fr_auto]">
                        <select
                            value={studentId}
                            onChange={(e) => setStudentId(e.target.value)}
                            className="rounded-md border border-plomo/20 px-3 py-2 text-sm"
                        >
                            <option value="">Seleccionar estudiante</option>
                            {catalog.students.map((s) => (
                                <option key={s.value} value={s.value}>
                                    {s.label}
                                </option>
                            ))}
                        </select>
                        <button
                            type="submit"
                            disabled={!studentId}
                            className="inline-flex items-center justify-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50"
                        >
                            <UserCheck className="h-4 w-4" />
                            Ver ficha del estudiante
                        </button>
                    </form>
                </Card>

                <TableContainer
                    title="Últimos registros"
                    description="Asistencia reciente en las secciones a su cargo."
                >
                    <table className="min-w-full text-left text-sm">
                        <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo">
                            <tr>
                                <th className="px-4 py-3">Fecha</th>
                                <th className="px-4 py-3">Estudiante</th>
                                <th className="px-4 py-3">Sección</th>
                                <th className="px-4 py-3">Estado</th>
                                <th className="px-4 py-3 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-plomo/10">
                            {recent_attendances.map((row) => (
                                <tr key={row.id}>
                                    <td className="px-4 py-3">{row.attendance_date}</td>
                                    <td className="px-4 py-3">
                                        <p className="font-medium text-navy-900">
                                            {row.student?.last_name}, {row.student?.first_name}
                                        </p>
                                        <p className="text-xs text-plomo">{row.student?.code}</p>
                                    </td>
                                    <td className="px-4 py-3 text-xs">
                                        {row.section?.name ?? '—'}
                                    </td>
                                    <td className="px-4 py-3 text-xs">{row.status}</td>
                                    <td className="px-4 py-3 text-right">
                                        {row.student ? (
                                            <Link
                                                href={route('teacher.students.show', row.student.id)}
                                                className="text-sm font-semibold text-navy-900 hover:underline"
                                            >
                                                Ver ficha
                                            </Link>
                                        ) : null}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </TableContainer>
            </PageContainer>
        </TeacherLayout>
    );
}
