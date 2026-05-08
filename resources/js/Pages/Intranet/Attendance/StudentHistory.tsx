import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { CheckCircle2, Clock3, FileCheck, XCircle } from 'lucide-react';

type HistoryRow = {
    id: number;
    attendance_date: string;
    status: string;
    observation: string | null;
    section?: { name: string };
    grade?: { name: string };
    educational_level?: { name: string };
    recorded_by?: { name: string };
};
type P = PageProps<{
    student: { id: number; code: string; first_name: string; last_name: string; document_number: string | null };
    history: { data: HistoryRow[] };
    metrics: { total: number; attendance_percentage: number; late_count: number; absence_count: number; justified_count: number };
}>;

export default function AttendanceStudentHistory() {
    const { student, history, metrics } = usePage<P>().props;

    const badgeClass = (value: string) => {
        if (value === 'presente') return 'bg-emerald-100 text-emerald-800';
        if (value === 'tarde') return 'bg-amber-100 text-amber-800';
        if (value === 'falta') return 'bg-red-100 text-red-800';
        return 'bg-blue-100 text-blue-800';
    };

    const statusIcon = (value: string) => {
        if (value === 'presente') return <CheckCircle2 className="h-3.5 w-3.5" />;
        if (value === 'tarde') return <Clock3 className="h-3.5 w-3.5" />;
        if (value === 'falta') return <XCircle className="h-3.5 w-3.5" />;
        return <FileCheck className="h-3.5 w-3.5" />;
    };

    return (
        <IntranetLayout title="Asistencia - Historial de estudiante">
            <Head title="Asistencia - Historial de estudiante" />
            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Asistencia', href: route('intranet.attendance.index') }, { label: 'Historial por estudiante' }]} />
                <SectionTitle title={`Historial: ${student.last_name}, ${student.first_name}`} description={`${student.code} · ${student.document_number ?? 'Sin documento'}`} actions={<Link href={route('intranet.attendance.index')} className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold">Volver</Link>} />

                <div className="mb-6 grid gap-4 md:grid-cols-4">
                    <Card><p className="text-xs uppercase text-plomo">Registros</p><p className="text-2xl font-bold text-navy-900">{metrics.total}</p></Card>
                    <Card><p className="text-xs uppercase text-plomo">% Asistencia</p><p className="mt-1 flex items-center gap-2 text-2xl font-bold text-emerald-700"><CheckCircle2 className="h-5 w-5" />{metrics.attendance_percentage}%</p></Card>
                    <Card><p className="text-xs uppercase text-plomo">Tardanzas</p><p className="mt-1 flex items-center gap-2 text-2xl font-bold text-amber-700"><Clock3 className="h-5 w-5" />{metrics.late_count}</p></Card>
                    <Card><p className="text-xs uppercase text-plomo">Faltas</p><p className="mt-1 flex items-center gap-2 text-2xl font-bold text-red-700"><XCircle className="h-5 w-5" />{metrics.absence_count}</p></Card>
                </div>

                <TableContainer title="Registro por fecha" description={`${history.data.length} filas en esta página.`}>
                    <table className="min-w-full text-left text-sm">
                        <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo"><tr><th className="px-4 py-3">Fecha</th><th className="px-4 py-3">Estado</th><th className="px-4 py-3">Nivel/Grado/Sección</th><th className="px-4 py-3">Observación</th><th className="px-4 py-3">Registró</th></tr></thead>
                        <tbody className="divide-y divide-plomo/10">
                            {history.data.map((item) => (
                                <tr key={item.id}>
                                    <td className="px-4 py-3">{item.attendance_date}</td>
                                    <td className="px-4 py-3">
                                        <span className={`inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold ${badgeClass(item.status)}`}>
                                            {statusIcon(item.status)}
                                            {item.status}
                                        </span>
                                    </td>
                                    <td className="px-4 py-3 text-xs">{item.educational_level?.name} / {item.grade?.name} / {item.section?.name}</td>
                                    <td className="px-4 py-3 text-xs">{item.observation ?? '—'}</td>
                                    <td className="px-4 py-3 text-xs">{item.recorded_by?.name ?? '—'}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </TableContainer>
            </PageContainer>
        </IntranetLayout>
    );
}

