import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { ClipboardCheck } from 'lucide-react';

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
    links: { records: string; history: string; reports: string };
    has_teaching_assignments?: boolean;
    teacher_portal_scoped?: boolean;
}>;

export default function TeacherGradesIndex() {
    const {
        recent_records,
        links,
        has_teaching_assignments,
        teacher_portal_scoped,
    } = usePage<P>().props;

    return (
        <TeacherLayout title="Notas">
            <Head title="Portal docente — Notas" />

            <PageContainer>
                {teacher_portal_scoped && has_teaching_assignments === false ? (
                    <div className="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        Sin asignaciones docentes en el año activo: no hay notas recientes
                        que mostrar en sus secciones.
                    </div>
                ) : null}

                <SectionTitle
                    title="Calificaciones"
                    description="Resumen de los últimos registros. El ingreso masivo de notas continúa en el ERP para reutilizar validaciones y políticas existentes."
                    actions={
                        <div className="flex flex-wrap gap-2">
                            <Link
                                href={links.records}
                                className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950"
                            >
                                <ClipboardCheck className="h-4 w-4" />
                                Registro de notas
                            </Link>
                            <Link
                                href={links.history}
                                className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900"
                            >
                                Historial académico
                            </Link>
                        </div>
                    }
                />

                <Card className="mb-6">
                    <p className="text-sm text-plomo">
                        Desde el ERP puede filtrar por año, sección, curso y
                        evaluación.{' '}
                        <Link
                            href={links.reports}
                            className="font-semibold text-navy-900 underline"
                        >
                            Ir a reportes académicos
                        </Link>
                        .
                    </p>
                </Card>

                <TableContainer
                    title="Últimas notas registradas"
                    description="Acceso directo al historial por estudiante desde la columna acción."
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
                                            {row.student?.last_name},{' '}
                                            {row.student?.first_name}
                                        </p>
                                        <p className="text-xs text-plomo">
                                            {row.student?.code}
                                        </p>
                                    </td>
                                    <td className="px-4 py-3 text-xs">
                                        {row.evaluation?.subject?.name ?? '—'}
                                    </td>
                                    <td className="px-4 py-3 text-xs">
                                        {row.evaluation?.title ?? '—'} (
                                        {row.evaluation?.period ?? '—'})
                                    </td>
                                    <td className="px-4 py-3 font-semibold text-navy-900">
                                        {row.score}
                                    </td>
                                    <td className="px-4 py-3 text-right">
                                        {row.student ? (
                                            <Link
                                                href={route(
                                                    'intranet.academic.grades.students.show',
                                                    row.student.id,
                                                )}
                                                className="text-sm font-semibold text-navy-900 hover:underline"
                                            >
                                                Historial
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
