import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { History, UserCheck } from 'lucide-react';
import { FormEvent, useState } from 'react';

type Row = {
    id: number;
    attendance_date: string;
    status: 'presente' | 'tarde' | 'falta' | 'justificado';
    observation: string | null;
    student?: { id: number; code: string; first_name: string; last_name: string };
    section?: { name: string };
};
type Paginator = { data: Row[] };
type P = PageProps<{
    filters: { student_id: string };
    catalog: { students: SelectOption[] };
    recent_attendances: Row[];
}>;

export default function AttendanceIndex() {
    const { filters, catalog, recent_attendances } = usePage<P>().props;
    const [studentId, setStudentId] = useState(filters.student_id || '');

    const goToStudentHistory = (e?: FormEvent) => {
        e?.preventDefault();
        if (!studentId) return;
        router.visit(route('intranet.attendance.students.show', studentId));
    };

    return (
        <IntranetLayout title="Asistencia - Historial por estudiante">
            <Head title="Asistencia - Historial por estudiante" />
            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Asistencia' }, { label: 'Historial por estudiante' }]} />
                <SectionTitle
                    title="Historial por estudiante"
                    description="Consulta individual del alumno y su trazabilidad de asistencia por fecha."
                    actions={
                        <div className="flex gap-2">
                            <Link href={route('intranet.attendance.create')} className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold">Registrar asistencia</Link>
                            <Link href={route('intranet.attendance.reports.index')} className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white">Ir a reportes</Link>
                        </div>
                    }
                />

                <Card className="mb-6">
                    <form onSubmit={goToStudentHistory} className="grid gap-4 md:grid-cols-[1fr_auto]">
                        <select value={studentId} onChange={(e) => setStudentId(e.target.value)} className="rounded-md border border-plomo/20 px-3 py-2 text-sm">
                            <option value="">Seleccionar estudiante</option>
                            {catalog.students.map((s) => <option key={s.value} value={s.value}>{s.label}</option>)}
                        </select>
                        <button type="submit" disabled={!studentId} className="inline-flex items-center justify-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">
                            <UserCheck className="h-4 w-4" />
                            Ver historial del alumno
                        </button>
                    </form>
                </Card>

                <TableContainer title="Últimos registros de asistencia" description="Vista rápida para localizar alumnos y abrir su historial detallado.">
                    <table className="min-w-full text-left text-sm">
                        <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo"><tr><th className="px-4 py-3">Fecha</th><th className="px-4 py-3">Estudiante</th><th className="px-4 py-3">Sección</th><th className="px-4 py-3">Estado</th><th className="px-4 py-3 text-right">Acción</th></tr></thead>
                        <tbody className="divide-y divide-plomo/10">
                            {recent_attendances.map((row) => (
                                <tr key={row.id}>
                                    <td className="px-4 py-3">{row.attendance_date}</td>
                                    <td className="px-4 py-3"><p className="font-medium text-navy-900">{row.student?.last_name}, {row.student?.first_name}</p><p className="text-xs text-plomo">{row.student?.code}</p></td>
                                    <td className="px-4 py-3 text-xs">{row.section?.name ?? '—'}</td>
                                    <td className="px-4 py-3 text-xs">{row.status}</td>
                                    <td className="px-4 py-3 text-right">{row.student ? <Link href={route('intranet.attendance.students.show', row.student.id)} className="inline-flex items-center gap-1 text-sm font-semibold text-navy-900 hover:underline"><History className="h-4 w-4" />Detalle</Link> : null}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </TableContainer>
            </PageContainer>
        </IntranetLayout>
    );
}

