import RecentAnnouncementsPanel from '@/Components/Announcements/RecentAnnouncementsPanel';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { StatsCard } from '@/Components/Intranet/StatsCard';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import { statsIcon } from '@/Components/Intranet/navIcons';
import {
    demoActivity,
    demoQuickLinks,
    demoStats,
} from '@/data/intranetDashboardDemo';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { ArrowRight, Sparkles } from 'lucide-react';

function roleSummary(roles: string[]): string {
    if (roles.includes('Administrador')) {
        return 'Vista de administración. Los módulos operativos se habilitarán en fases siguientes del roadmap.';
    }
    if (roles.includes('Secretaria')) {
        return 'Vista orientada a secretaría, matrículas y pensiones (en construcción).';
    }
    if (roles.includes('Docente')) {
        return 'Vista orientada a docentes y evaluaciones (en construcción).';
    }
    if (roles.includes('Apoderado')) {
        return 'Vista orientada a apoderados: pagos y seguimiento (en construcción).';
    }
    return 'Vista de estudiante: progreso y tutor (en construcción).';
}

export default function Dashboard() {
    const { auth } = usePage<PageProps>().props;
    const roles = auth.user?.roles ?? [];

    return (
        <IntranetLayout title="Panel principal">
            <Head title="Panel — Horizonte" />

            <PageContainer>
                <SectionTitle
                    title={`Hola, ${auth.user?.name?.split(' ')[0] ?? 'equipo'}`}
                    description="Resumen institucional con datos de demostración. Las métricas reales se conectarán cuando los módulos estén activos."
                />

                <RecentAnnouncementsPanel />

                <div className="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    {demoStats.map((s) => (
                        <StatsCard
                            key={s.title}
                            title={s.title}
                            value={s.value}
                            subtitle={s.subtitle}
                            icon={statsIcon(s.iconKey)}
                            trend={s.trend}
                            accent={s.accent}
                        />
                    ))}
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    <div className="space-y-6 lg:col-span-2">
                        <Card>
                            <div className="mb-4 flex items-center justify-between gap-2">
                                <h2 className="text-sm font-bold uppercase tracking-wider text-navy-900">
                                    Actividad reciente
                                </h2>
                                <span className="rounded-full bg-brand-yellow/20 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-navy-900">
                                    Demo
                                </span>
                            </div>
                            <ul className="divide-y divide-plomo/10">
                                {demoActivity.map((row) => (
                                    <li
                                        key={row.id}
                                        className="flex items-start gap-3 py-3 first:pt-0 last:pb-0"
                                    >
                                        <span
                                            className={`mt-0.5 h-2 w-2 shrink-0 rounded-full ${
                                                row.tone === 'red'
                                                    ? 'bg-brand-red'
                                                    : row.tone === 'navy'
                                                      ? 'bg-navy-900'
                                                      : 'bg-plomo-light'
                                            }`}
                                        />
                                        <div className="min-w-0 flex-1">
                                            <p className="text-sm font-medium text-navy-900">
                                                {row.title}
                                            </p>
                                            <p className="text-xs text-plomo">
                                                {row.time}
                                            </p>
                                        </div>
                                        <ArrowRight className="h-4 w-4 shrink-0 text-plomo-light" />
                                    </li>
                                ))}
                            </ul>
                        </Card>

                        <TableContainer
                            title="Vista previa de tabla"
                            description="Contenedor reutilizable para listados (estudiantes, pagos, etc.). Sin datos reales."
                            toolbar={
                                <span className="rounded-md bg-navy-50 px-2 py-1 text-xs font-medium text-navy-900">
                                    Próximamente
                                </span>
                            }
                        >
                            <table className="min-w-full text-left text-sm">
                                <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase tracking-wide text-plomo">
                                    <tr>
                                        <th className="px-4 py-3 sm:px-6">
                                            Concepto
                                        </th>
                                        <th className="px-4 py-3 sm:px-6">
                                            Estado
                                        </th>
                                        <th className="hidden px-4 py-3 sm:table-cell sm:px-6">
                                            Fecha
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-plomo/10 text-navy-900">
                                    <tr className="bg-white">
                                        <td className="px-4 py-4 sm:px-6">
                                            Matrícula regular 2026 (demo)
                                        </td>
                                        <td className="px-4 py-4 sm:px-6">
                                            <span className="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-800">
                                                En revisión
                                            </span>
                                        </td>
                                        <td className="hidden px-4 py-4 text-plomo sm:table-cell sm:px-6">
                                            — / — / —
                                        </td>
                                    </tr>
                                    <tr className="bg-white">
                                        <td className="px-4 py-4 sm:px-6">
                                            Cuota pensión marzo (demo)
                                        </td>
                                        <td className="px-4 py-4 sm:px-6">
                                            <span className="rounded-full bg-brand-yellow/20 px-2 py-0.5 text-xs font-medium text-navy-900">
                                                Pendiente
                                            </span>
                                        </td>
                                        <td className="hidden px-4 py-4 text-plomo sm:table-cell sm:px-6">
                                            — / — / —
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </TableContainer>
                    </div>

                    <div className="space-y-6">
                        <Card>
                            <div className="mb-4 flex items-center gap-2">
                                <Sparkles className="h-4 w-4 text-brand-yellow" />
                                <h2 className="text-sm font-bold uppercase tracking-wider text-navy-900">
                                    Tu contexto
                                </h2>
                            </div>
                            <p className="text-sm leading-relaxed text-plomo">
                                {roleSummary(roles)}
                            </p>
                        </Card>

                        <Card>
                            <h2 className="mb-4 text-sm font-bold uppercase tracking-wider text-navy-900">
                                Accesos rápidos
                            </h2>
                            <div className="grid gap-2">
                                {demoQuickLinks.map((link) => (
                                    <button
                                        key={link.label}
                                        type="button"
                                        disabled={link.disabled}
                                        className="flex w-full items-center justify-between rounded-lg border border-plomo/15 bg-navy-50/50 px-3 py-2.5 text-left text-sm font-medium text-navy-900 transition hover:border-navy-900/20 hover:bg-white disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        {link.label}
                                        <ArrowRight className="h-4 w-4 text-plomo" />
                                    </button>
                                ))}
                            </div>
                            <p className="mt-3 text-xs text-plomo">
                                Los accesos se habilitarán con cada módulo del
                                roadmap.
                            </p>
                        </Card>
                    </div>
                </div>
            </PageContainer>
        </IntranetLayout>
    );
}
