import { TeacherAssignmentsEmpty } from '@/Components/Teacher/AssignmentsOverview';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { ClipboardCheck, FileDown } from 'lucide-react';
import { useState } from 'react';

type GradeRow = {
    id: number;
    score: string | number;
    student?: {
        id: number;
        code: string;
        first_name: string;
        last_name: string;
    };
    evaluation?: {
        id: number;
        title: string;
        period: string;
        subject?: { name: string };
    };
};

type P = PageProps<{
    recent_records: GradeRow[];
    filters: { section_id: string; subject_id: string };
    catalog: { sections: SelectOption[]; subjects: SelectOption[] };
    links: { records: string; reports: string };
    has_teaching_assignments?: boolean;
    teacher_portal_scoped?: boolean;
    empty_message?: string;
}>;

export default function TeacherGradesIndex() {
    const {
        recent_records,
        filters,
        catalog,
        links,
        has_teaching_assignments,
        teacher_portal_scoped,
        empty_message,
    } = usePage<P>().props;

    const [sectionId, setSectionId] = useState(filters.section_id || '');
    const [subjectId, setSubjectId] = useState(filters.subject_id || '');

    const applyFilters = (nextSection: string, nextSubject: string) => {
        router.get(
            route('teacher.grades.index'),
            {
                section_id: nextSection || undefined,
                subject_id: nextSubject || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const recordsHref = (() => {
        const params = new URLSearchParams();
        if (sectionId) params.set('section_id', sectionId);
        if (subjectId) params.set('subject_id', subjectId);
        const q = params.toString();
        return q ? `${links.records}?${q}` : links.records;
    })();

    return (
        <TeacherLayout title="Notas">
            <Head title="Portal docente — Notas" />

            <PageContainer>
                {teacher_portal_scoped && has_teaching_assignments === false ? (
                    <div className="mb-6">
                        <TeacherAssignmentsEmpty message={empty_message ?? ''} />
                    </div>
                ) : null}

                <SectionTitle
                    title="Calificaciones"
                    description="Notas y evaluaciones de los cursos y secciones que tiene asignados."
                    actions={
                        <div className="flex flex-wrap gap-2">
                            <Link
                                href={recordsHref}
                                className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                            >
                                <ClipboardCheck className="h-4 w-4" />
                                Registro de notas
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

                {(catalog.sections.length > 0 || catalog.subjects.length > 0) && (
                    <Card className="mb-6">
                        <div className="grid gap-4 md:grid-cols-2">
                            {catalog.sections.length > 0 ? (
                                <div>
                                    <label className="block text-xs font-semibold uppercase text-plomo">
                                        Sección asignada
                                    </label>
                                    <select
                                        value={sectionId}
                                        onChange={(e) => {
                                            setSectionId(e.target.value);
                                            applyFilters(e.target.value, subjectId);
                                        }}
                                        className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"
                                    >
                                        <option value="">Todas mis secciones</option>
                                        {catalog.sections.map((s) => (
                                            <option key={s.value} value={s.value}>
                                                {s.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            ) : null}
                            {catalog.subjects.length > 0 ? (
                                <div>
                                    <label className="block text-xs font-semibold uppercase text-plomo">
                                        Curso asignado
                                    </label>
                                    <select
                                        value={subjectId}
                                        onChange={(e) => {
                                            setSubjectId(e.target.value);
                                            applyFilters(sectionId, e.target.value);
                                        }}
                                        className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"
                                    >
                                        <option value="">Todos mis cursos</option>
                                        {catalog.subjects.map((s) => (
                                            <option key={s.value} value={s.value}>
                                                {s.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            ) : null}
                        </div>
                    </Card>
                )}

                <Card className="mb-6">
                    <p className="text-sm text-plomo">
                        Para cargar o actualizar notas, use{' '}
                        <Link href={recordsHref} className="font-semibold text-navy-900 underline">
                            Registro de notas
                        </Link>
                        . Solo verá evaluaciones vinculadas a sus asignaciones.
                    </p>
                </Card>

                <TableContainer
                    title="Últimas notas registradas"
                    description="Calificaciones recientes en sus secciones y cursos."
                >
                    <table className="min-w-full text-left text-sm">
                        <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo">
                            <tr>
                                <th className="px-4 py-3">Estudiante</th>
                                <th className="px-4 py-3">Curso</th>
                                <th className="px-4 py-3">Evaluación</th>
                                <th className="px-4 py-3">Nota</th>
                                <th className="px-4 py-3 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-plomo/10">
                            {recent_records.map((row) => (
                                <tr key={row.id}>
                                    <td className="px-4 py-3">
                                        <p className="font-medium text-navy-900">
                                            {row.student?.last_name}, {row.student?.first_name}
                                        </p>
                                        <p className="text-xs text-plomo">{row.student?.code}</p>
                                    </td>
                                    <td className="px-4 py-3 text-xs">
                                        {row.evaluation?.subject?.name ?? '—'}
                                    </td>
                                    <td className="px-4 py-3 text-xs">
                                        {row.evaluation?.title ?? '—'} ({row.evaluation?.period ?? '—'})
                                    </td>
                                    <td className="px-4 py-3 font-semibold text-navy-900">
                                        {row.score}
                                    </td>
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
