import { AppCard } from '@/Components/App/AppCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { AppStatCard } from '@/Components/App/AppStatCard';
import { AppTable } from '@/Components/App/AppTable';
import RecentAnnouncementsPanel from '@/Components/Announcements/RecentAnnouncementsPanel';
import RecentNotificationsPanel from '@/Components/Notifications/RecentNotificationsPanel';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { statsIcon } from '@/Components/Intranet/navIcons';
import {
    demoActivity,
    demoStats,
    quickLinksForRoles,
} from '@/data/intranetDashboardDemo';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight, Sparkles } from 'lucide-react';

function roleSummary(roles: string[]): string {
    if (roles.includes('Administrador')) {
        return 'Vista ejecutiva: ERP, LMS, CMS, analítica, seguridad y comunicación operativa desde un solo panel.';
    }
    if (roles.includes('Secretaria')) {
        return 'Gestión de matrículas, estudiantes, pensiones y cobranza con acceso a reportes operativos.';
    }
    if (roles.includes('Docente')) {
        return 'Accede al portal docente para asistencia, notas, aulas virtuales, diagnósticos e IA pedagógica.';
    }
    if (roles.includes('Apoderado')) {
        return 'Seguimiento de pagos y comunicados institucionales (portal en evolución).';
    }
    return 'Resumen institucional. Usa el portal estudiante para LMS, tutor IA y gamificación.';
}

export default function Dashboard() {
    const { auth } = usePage<PageProps>().props;
    const roles = auth.user?.roles ?? [];
    const quickLinks = quickLinksForRoles(roles);

    return (
        <IntranetLayout title="Panel principal">
            <Head title="Panel — Horizonte" />

            <PageContainer>
                <AppPageHeader
                    title={`Hola, ${auth.user?.name?.split(' ')[0] ?? 'equipo'}`}
                    description="Resumen institucional. Las tarjetas superiores usan datos de demostración; los accesos rápidos enlazan a módulos activos."
                    eyebrow="Panel ejecutivo"
                />

                <RecentAnnouncementsPanel />
                <RecentNotificationsPanel title="Alertas operativas" />

                <div className="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    {demoStats.map((s) => (
                        <AppStatCard
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
                        <AppCard>
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
                        </AppCard>

                        <AppTable
                            title="Vista previa de tabla"
                            description="Contenedor reutilizable para listados (estudiantes, pagos, etc.). Sin datos reales."
                            toolbar={
                                <span className="rounded-md bg-navy-50 px-2 py-1 text-xs font-medium text-navy-900">
                                    Próximamente
                                </span>
                            }
                        >
                            <table className="min-w-full text-left text-sm">
                                <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase tracking-wide text-plomo dark:border-white/10 dark:bg-slate-800/80 dark:text-slate-400">
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
                                <tbody className="divide-y divide-plomo/10 text-navy-900 dark:divide-white/10 dark:text-slate-100">
                                    <tr className="bg-white dark:bg-slate-900/50">
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
                        </AppTable>
                    </div>

                    <div className="space-y-6">
                        <AppCard>
                            <div className="mb-4 flex items-center gap-2">
                                <Sparkles className="h-4 w-4 text-brand-yellow" />
                                <h2 className="text-sm font-bold uppercase tracking-wider text-navy-900">
                                    Tu contexto
                                </h2>
                            </div>
                            <p className="text-sm leading-relaxed text-plomo">
                                {roleSummary(roles)}
                            </p>
                        </AppCard>

                        <AppCard>
                            <h2 className="mb-4 text-sm font-bold uppercase tracking-wider text-navy-900">
                                Accesos rápidos
                            </h2>
                            <div className="grid gap-2">
                                {quickLinks.map((link) =>
                                    link.disabled || !link.href ? (
                                        <button
                                            key={link.label}
                                            type="button"
                                            disabled
                                            className="flex w-full items-center justify-between rounded-lg border border-plomo/15 bg-navy-50/50 px-3 py-2.5 text-left text-sm font-medium text-navy-900 opacity-50 dark:border-white/10 dark:bg-slate-800/50 dark:text-slate-200"
                                        >
                                            {link.label}
                                            <ArrowRight className="h-4 w-4 text-plomo" />
                                        </button>
                                    ) : (
                                        <Link
                                            key={link.label}
                                            href={link.href}
                                            className="flex w-full items-center justify-between rounded-lg border border-plomo/15 bg-navy-50/50 px-3 py-2.5 text-left text-sm font-medium text-navy-900 transition hover:border-navy-900/20 hover:bg-white dark:border-white/10 dark:bg-slate-800/50 dark:text-slate-100 dark:hover:border-brand-yellow/30 dark:hover:bg-slate-800"
                                        >
                                            {link.label}
                                            <ArrowRight className="h-4 w-4 text-plomo" />
                                        </Link>
                                    )
                                )}
                            </div>
                            <p className="mt-3 text-xs text-plomo dark:text-slate-400">
                                Enlaces operativos según tu rol institucional.
                            </p>
                        </AppCard>
                    </div>
                </div>
            </PageContainer>
        </IntranetLayout>
    );
}
