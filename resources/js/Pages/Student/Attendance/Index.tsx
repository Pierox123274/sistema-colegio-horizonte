import AttendanceStatusBadge from '@/Components/Student/AttendanceStatusBadge';
import StudentPortalEmpty from '@/Components/Student/StudentPortalEmpty';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { StatsCard } from '@/Components/Intranet/StatsCard';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import {
    CalendarCheck,
    CheckCircle2,
    Clock3,
    FileCheck,
    Filter,
    XCircle,
} from 'lucide-react';
import { FormEvent, useState } from 'react';

type HistoryRow = {
    id: number;
    attendance_date_label: string;
    status: string;
    status_label: string;
    observation: string | null;
    section?: { name: string };
    grade?: { name: string };
    recorded_by?: { name: string };
};

type CatalogOption = { value: string; label: string };

type Props = PageProps<{
    student: { full_name: string; code: string } | null;
    history: { data: HistoryRow[]; links?: unknown[] } | null;
    metrics: {
        total: number;
        attendance_percentage: number;
        present_count: number;
        late_count: number;
        absence_count: number;
        justified_count: number;
    };
    filters: {
        date_from: string;
        date_to: string;
        status: string;
        section_id: string;
    };
    catalog: {
        statuses: CatalogOption[];
        sections: CatalogOption[];
    };
    has_student: boolean;
    portal_scoped: boolean;
    empty_message: string;
}>;

export default function StudentAttendanceIndex() {
    const {
        student,
        history,
        metrics,
        filters,
        catalog,
        has_student,
        portal_scoped,
        empty_message,
    } = usePage<Props>().props;

    const [localFilters, setLocalFilters] = useState(filters);

    const applyFilters = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(route('student.attendance.index'), localFilters, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const clearFilters = () => {
        const empty = {
            date_from: '',
            date_to: '',
            status: '',
            section_id: '',
        };
        setLocalFilters(empty);
        router.get(route('student.attendance.index'), empty, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const hasActiveFilters = Object.values(filters).some((v) => v !== '');
    const rows = history?.data ?? [];

    return (
        <StudentLayout title="Mi asistencia">
            <Head title="Mi asistencia" />
            <PageContainer>
                <SectionTitle
                    title="Mi asistencia"
                    description="Revisa tu porcentaje de asistencia y el detalle por fecha."
                />

                {!has_student ? (
                    <StudentPortalEmpty message={empty_message} portalScoped={portal_scoped} />
                ) : (
                    <>
                        {student && (
                            <p className="mb-6 text-sm text-plomo">
                                Estudiante:{' '}
                                <span className="font-semibold text-navy-900">
                                    {student.full_name}
                                </span>{' '}
                                · Código {student.code}
                            </p>
                        )}

                        <div className="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                            <StatsCard
                                title="% Asistencia"
                                value={`${metrics.attendance_percentage}%`}
                                subtitle="Presente + justificado"
                                icon={CalendarCheck}
                                accent="navy"
                            />
                            <StatsCard
                                title="Presentes"
                                value={String(metrics.present_count)}
                                subtitle="Días asistidos"
                                icon={CheckCircle2}
                                accent="yellow"
                            />
                            <StatsCard
                                title="Tardanzas"
                                value={String(metrics.late_count)}
                                subtitle="Llegadas tarde"
                                icon={Clock3}
                                accent="red"
                            />
                            <StatsCard
                                title="Faltas"
                                value={String(metrics.absence_count)}
                                subtitle="Inasistencias"
                                icon={XCircle}
                                accent="navy"
                            />
                            <StatsCard
                                title="Justificados"
                                value={String(metrics.justified_count)}
                                subtitle="Con sustento"
                                icon={FileCheck}
                                accent="yellow"
                            />
                        </div>

                        <Card className="mb-6">
                            <div className="flex flex-wrap items-center justify-between gap-3">
                                <div className="flex items-center gap-2 text-sm font-semibold text-navy-900">
                                    <Filter className="h-4 w-4 text-plomo" />
                                    Filtrar registros
                                </div>
                                {hasActiveFilters && (
                                    <button
                                        type="button"
                                        onClick={clearFilters}
                                        className="text-xs font-semibold text-brand-red hover:underline"
                                    >
                                        Limpiar filtros
                                    </button>
                                )}
                            </div>
                            <form
                                onSubmit={applyFilters}
                                className="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-5"
                            >
                                <label className="block text-sm">
                                    <span className="mb-1 block text-xs font-medium text-plomo">
                                        Desde
                                    </span>
                                    <input
                                        type="date"
                                        value={localFilters.date_from}
                                        onChange={(e) =>
                                            setLocalFilters((f) => ({
                                                ...f,
                                                date_from: e.target.value,
                                            }))
                                        }
                                        className="w-full rounded-lg border border-plomo/25 px-3 py-2 text-sm"
                                    />
                                </label>
                                <label className="block text-sm">
                                    <span className="mb-1 block text-xs font-medium text-plomo">
                                        Hasta
                                    </span>
                                    <input
                                        type="date"
                                        value={localFilters.date_to}
                                        onChange={(e) =>
                                            setLocalFilters((f) => ({
                                                ...f,
                                                date_to: e.target.value,
                                            }))
                                        }
                                        className="w-full rounded-lg border border-plomo/25 px-3 py-2 text-sm"
                                    />
                                </label>
                                <label className="block text-sm">
                                    <span className="mb-1 block text-xs font-medium text-plomo">
                                        Estado
                                    </span>
                                    <select
                                        value={localFilters.status}
                                        onChange={(e) =>
                                            setLocalFilters((f) => ({
                                                ...f,
                                                status: e.target.value,
                                            }))
                                        }
                                        className="w-full rounded-lg border border-plomo/25 px-3 py-2 text-sm"
                                    >
                                        <option value="">Todos</option>
                                        {catalog.statuses.map((opt) => (
                                            <option key={opt.value} value={opt.value}>
                                                {opt.label}
                                            </option>
                                        ))}
                                    </select>
                                </label>
                                <label className="block text-sm">
                                    <span className="mb-1 block text-xs font-medium text-plomo">
                                        Sección
                                    </span>
                                    <select
                                        value={localFilters.section_id}
                                        onChange={(e) =>
                                            setLocalFilters((f) => ({
                                                ...f,
                                                section_id: e.target.value,
                                            }))
                                        }
                                        className="w-full rounded-lg border border-plomo/25 px-3 py-2 text-sm"
                                        disabled={catalog.sections.length === 0}
                                    >
                                        <option value="">Todas</option>
                                        {catalog.sections.map((opt) => (
                                            <option key={opt.value} value={opt.value}>
                                                {opt.label}
                                            </option>
                                        ))}
                                    </select>
                                </label>
                                <div className="flex items-end">
                                    <button
                                        type="submit"
                                        className="w-full rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white hover:bg-navy-800"
                                    >
                                        Aplicar
                                    </button>
                                </div>
                            </form>
                        </Card>

                        <Card>
                            <h3 className="text-base font-semibold text-navy-900">
                                Detalle por fecha
                            </h3>
                            <p className="mt-1 text-sm text-plomo">
                                {hasActiveFilters
                                    ? 'Resultados según los filtros aplicados.'
                                    : 'Todos tus registros de asistencia.'}
                            </p>

                            {rows.length === 0 ? (
                                <p className="mt-6 rounded-lg border border-dashed border-plomo/25 bg-slate-50 px-4 py-8 text-center text-sm text-plomo">
                                    {hasActiveFilters
                                        ? 'No hay registros con los filtros seleccionados.'
                                        : 'Aún no tienes registros de asistencia.'}
                                </p>
                            ) : (
                                <div className="mt-4 overflow-x-auto">
                                    <table className="min-w-full text-sm">
                                        <thead>
                                            <tr className="border-b border-plomo/15 text-left text-xs font-semibold uppercase tracking-wide text-plomo">
                                                <th className="px-3 py-3">Fecha</th>
                                                <th className="px-3 py-3">Sección</th>
                                                <th className="px-3 py-3">Estado</th>
                                                <th className="px-3 py-3">Observación</th>
                                                <th className="px-3 py-3">Registró</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {rows.map((row) => (
                                                <tr
                                                    key={row.id}
                                                    className="border-b border-plomo/10 hover:bg-navy-50/40"
                                                >
                                                    <td className="px-3 py-3 whitespace-nowrap font-medium text-navy-900">
                                                        {row.attendance_date_label}
                                                    </td>
                                                    <td className="px-3 py-3">
                                                        {row.section?.name ?? '—'}
                                                        {row.grade?.name && (
                                                            <span className="block text-xs text-plomo">
                                                                {row.grade.name}
                                                            </span>
                                                        )}
                                                    </td>
                                                    <td className="px-3 py-3">
                                                        <AttendanceStatusBadge
                                                            status={row.status}
                                                            label={row.status_label}
                                                        />
                                                    </td>
                                                    <td className="max-w-xs px-3 py-3 text-plomo">
                                                        {row.observation?.trim()
                                                            ? row.observation
                                                            : '—'}
                                                    </td>
                                                    <td className="px-3 py-3 text-plomo">
                                                        {row.recorded_by?.name ?? '—'}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </Card>
                    </>
                )}
            </PageContainer>
        </StudentLayout>
    );
}
