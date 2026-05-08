import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { CheckCircle2, Clock3, FileCheck, Filter, XCircle } from 'lucide-react';
import { FormEvent, useState } from 'react';

type Row = {
    id: number;
    attendance_date: string;
    status: string;
    observation: string | null;
    student?: { id: number; code: string; first_name: string; last_name: string };
    section?: { name: string };
    grade?: { name: string };
    educational_level?: { name: string };
};
type Paginator = { data: Row[] };
type P = PageProps<{
    attendances: Paginator;
    filters: { search: string; date: string; date_from: string; date_to: string; section_id: string; student_id: string; status: string };
    catalog: { statuses: SelectOption[]; sections: SelectOption[]; students: SelectOption[] };
    metrics: { total: number; attendance_percentage: number; late_count: number; absence_count: number; justified_count: number };
}>;

export default function AttendanceReports() {
    const { attendances, filters, catalog, metrics } = usePage<P>().props;
    const [search, setSearch] = useState(filters.search || '');
    const [date, setDate] = useState(filters.date || '');
    const [dateFrom, setDateFrom] = useState(filters.date_from || '');
    const [dateTo, setDateTo] = useState(filters.date_to || '');
    const [sectionId, setSectionId] = useState(filters.section_id || '');
    const [studentId, setStudentId] = useState(filters.student_id || '');
    const [status, setStatus] = useState(filters.status || '');

    const apply = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(route('intranet.attendance.reports.index'), { search: search || undefined, date: date || undefined, date_from: dateFrom || undefined, date_to: dateTo || undefined, section_id: sectionId || undefined, student_id: studentId || undefined, status: status || undefined }, { preserveState: true, replace: true });
    };

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

    const exportParams = new URLSearchParams(
        Object.entries({
            search,
            date,
            date_from: dateFrom,
            date_to: dateTo,
            section_id: sectionId,
            student_id: studentId,
            status,
        }).filter(([, v]) => Boolean(v)) as [string, string][],
    ).toString();

    return (
        <IntranetLayout title="Asistencia - Reportes">
            <Head title="Asistencia - Reportes" />
            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Asistencia' }, { label: 'Reportes de asistencia' }]} />
                <SectionTitle
                    title="Reportes de asistencia"
                    description="Análisis global por fecha, sección y estado; incluye exportaciones PDF/CSV."
                    actions={
                        <div className="flex gap-2">
                            <Link href={route('intranet.attendance.index')} className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold">Ir a historial</Link>
                            <a href={`${route('intranet.attendance.reports.export.pdf')}${exportParams ? `?${exportParams}` : ''}`} className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold">Descargar PDF</a>
                            <a href={`${route('intranet.attendance.reports.export.excel')}${exportParams ? `?${exportParams}` : ''}`} className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold">Descargar CSV</a>
                        </div>
                    }
                />

                <div className="mb-6 grid gap-4 md:grid-cols-5">
                    <Card><p className="text-xs uppercase text-plomo">Registros</p><p className="text-2xl font-bold text-navy-900">{metrics.total}</p></Card>
                    <Card><p className="text-xs uppercase text-plomo">% Asistencia</p><p className="mt-1 flex items-center gap-2 text-2xl font-bold text-emerald-700"><CheckCircle2 className="h-5 w-5" />{metrics.attendance_percentage}%</p></Card>
                    <Card><p className="text-xs uppercase text-plomo">Tardanzas</p><p className="mt-1 flex items-center gap-2 text-2xl font-bold text-amber-700"><Clock3 className="h-5 w-5" />{metrics.late_count}</p></Card>
                    <Card><p className="text-xs uppercase text-plomo">Faltas</p><p className="mt-1 flex items-center gap-2 text-2xl font-bold text-red-700"><XCircle className="h-5 w-5" />{metrics.absence_count}</p></Card>
                    <Card><p className="text-xs uppercase text-plomo">Justificados</p><p className="mt-1 flex items-center gap-2 text-2xl font-bold text-blue-700"><FileCheck className="h-5 w-5" />{metrics.justified_count}</p></Card>
                </div>

                <Card className="mb-6">
                    <form onSubmit={apply} className="grid gap-4 md:grid-cols-4">
                        <input type="search" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Buscar estudiante u observación" className="rounded-md border border-plomo/20 px-3 py-2 text-sm" />
                        <input type="date" value={date} onChange={(e) => setDate(e.target.value)} className="rounded-md border border-plomo/20 px-3 py-2 text-sm" />
                        <input type="date" value={dateFrom} onChange={(e) => setDateFrom(e.target.value)} className="rounded-md border border-plomo/20 px-3 py-2 text-sm" />
                        <input type="date" value={dateTo} onChange={(e) => setDateTo(e.target.value)} className="rounded-md border border-plomo/20 px-3 py-2 text-sm" />
                        <select value={sectionId} onChange={(e) => setSectionId(e.target.value)} className="rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Todas las secciones</option>{catalog.sections.map((s) => <option key={s.value} value={s.value}>{s.label}</option>)}</select>
                        <select value={studentId} onChange={(e) => setStudentId(e.target.value)} className="rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Todos los estudiantes</option>{catalog.students.map((s) => <option key={s.value} value={s.value}>{s.label}</option>)}</select>
                        <select value={status} onChange={(e) => setStatus(e.target.value)} className="rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Todos los estados</option>{catalog.statuses.map((s) => <option key={s.value} value={s.value}>{s.label}</option>)}</select>
                        <button type="submit" className="inline-flex items-center justify-center gap-2 rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold"><Filter className="h-4 w-4" />Filtrar</button>
                    </form>
                </Card>

                <TableContainer title="Detalle de asistencia" description={`${attendances.data.length} registros en esta página.`}>
                    <table className="min-w-full text-left text-sm">
                        <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase text-plomo"><tr><th className="px-4 py-3">Fecha</th><th className="px-4 py-3">Estudiante</th><th className="px-4 py-3">Nivel/Grado/Sección</th><th className="px-4 py-3">Estado</th><th className="px-4 py-3">Observación</th><th className="px-4 py-3 text-right">Acción</th></tr></thead>
                        <tbody className="divide-y divide-plomo/10">
                            {attendances.data.map((row) => (
                                <tr key={row.id}>
                                    <td className="px-4 py-3">{row.attendance_date}</td>
                                    <td className="px-4 py-3"><p className="font-medium text-navy-900">{row.student?.last_name}, {row.student?.first_name}</p><p className="text-xs text-plomo">{row.student?.code}</p></td>
                                    <td className="px-4 py-3 text-xs">{row.educational_level?.name} / {row.grade?.name} / {row.section?.name}</td>
                                    <td className="px-4 py-3"><span className={`inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold ${badgeClass(row.status)}`}>{statusIcon(row.status)}{row.status}</span></td>
                                    <td className="px-4 py-3 text-xs">{row.observation ?? '—'}</td>
                                    <td className="px-4 py-3 text-right">{row.student ? <Link href={route('intranet.attendance.students.show', row.student.id)} className="text-sm font-semibold text-navy-900 hover:underline">Ver historial</Link> : null}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </TableContainer>
            </PageContainer>
        </IntranetLayout>
    );
}

