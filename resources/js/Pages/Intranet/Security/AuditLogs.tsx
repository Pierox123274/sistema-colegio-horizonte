import { AppPageHeader } from '@/Components/App/AppPageHeader';
import AuditActionBadge from '@/Components/Security/AuditActionBadge';
import AuditLogMobileCard from '@/Components/Security/AuditLogMobileCard';
import AuditResultBadge from '@/Components/Security/AuditResultBadge';
import AuditSeverityBadge from '@/Components/Security/AuditSeverityBadge';
import AuditTimeline, { type AuditTimelineItem } from '@/Components/Security/AuditTimeline';
import SecurityFilterBar from '@/Components/Security/SecurityFilterBar';
import SecurityFlash from '@/Components/Security/SecurityFlash';
import SecurityNetworkCell from '@/Components/Security/SecurityNetworkCell';
import SecurityPagination from '@/Components/Security/SecurityPagination';
import SecuritySummaryKpis from '@/Components/Security/SecuritySummaryKpis';
import SecurityTableScroll from '@/Components/Security/SecurityTableScroll';
import SecurityUserCell from '@/Components/Security/SecurityUserCell';
import { securityFieldClass, securityTdClass, securityThClass } from '@/Components/Security/securityUi';
import { EmptyState } from '@/Components/Intranet/EmptyState';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import {
    Activity,
    AlertTriangle,
    Calendar,
    Monitor,
    ScrollText,
    ShieldAlert,
} from 'lucide-react';
import { FormEvent, useState, type ReactNode } from 'react';

type P = PageProps<{
    filters: Record<string, string>;
    stats: {
        total_events: number;
        critical_events: number;
        events_today: number;
        failed_logins_24h: number;
        active_sessions: number;
    };
    timeline: AuditTimelineItem[];
    logs: {
        data: AuditTimelineItem[];
        links: { url: string | null; label: string; active: boolean }[];
        meta: { total: number };
    };
    catalog: { modules: SelectOption[]; actions: SelectOption[]; severities: SelectOption[] };
}>;

function FilterField({
    label,
    children,
}: {
    label: string;
    children: ReactNode;
}) {
    return (
        <label className="block text-sm">
            <span className="mb-1 block text-xs font-semibold uppercase tracking-wide text-plomo">
                {label}
            </span>
            {children}
        </label>
    );
}

export default function SecurityAuditLogs() {
    const { filters, stats, timeline, logs, catalog, flash } = usePage<P>().props;
    const [search, setSearch] = useState(String(filters.search ?? ''));
    const [module, setModule] = useState(String(filters.module ?? ''));
    const [action, setAction] = useState(String(filters.action ?? ''));
    const [severity, setSeverity] = useState(String(filters.severity ?? ''));
    const [date_from, setDateFrom] = useState(String(filters.date_from ?? ''));
    const [date_to, setDateTo] = useState(String(filters.date_to ?? ''));

    const filterPayload = () => ({
        search: search || undefined,
        module: module || undefined,
        action: action || undefined,
        severity: severity || undefined,
        date_from: date_from || undefined,
        date_to: date_to || undefined,
    });

    const apply = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(route('intranet.security.audit-logs.index'), filterPayload(), {
            preserveState: true,
            replace: true,
        });
    };

    const clear = () => {
        setSearch('');
        setModule('');
        setAction('');
        setSeverity('');
        setDateFrom('');
        setDateTo('');
        router.get(route('intranet.security.audit-logs.index'), {}, { replace: true });
    };

    const rows = logs.data ?? [];

    return (
        <IntranetLayout title="Auditoría institucional">
            <Head title="Auditoría — Horizonte" />

            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Seguridad y auditoría' },
                        { label: 'Auditoría' },
                    ]}
                />

                {flash?.success ? <SecurityFlash message={flash.success} /> : null}

                <AppPageHeader
                    title="Auditoría institucional"
                    description="Trazabilidad ISO 27001: quién hizo qué, cuándo, desde dónde y con qué resultado."
                />

                <SecuritySummaryKpis
                    items={[
                        {
                            title: 'Total eventos',
                            value: stats.total_events,
                            icon: ScrollText,
                        },
                        {
                            title: 'Eventos críticos',
                            value: stats.critical_events,
                            icon: ShieldAlert,
                            accent: 'red',
                        },
                        {
                            title: 'Eventos hoy',
                            value: stats.events_today,
                            icon: Calendar,
                        },
                        {
                            title: 'Intentos fallidos (24h)',
                            value: stats.failed_logins_24h,
                            icon: AlertTriangle,
                            accent: stats.failed_logins_24h > 0 ? 'red' : 'navy',
                        },
                        {
                            title: 'Sesiones activas',
                            value: stats.active_sessions,
                            icon: Monitor,
                        },
                        {
                            title: 'En esta página',
                            value: rows.length,
                            subtitle: `de ${logs.meta?.total ?? rows.length} filtrados`,
                            icon: Activity,
                            accent: 'yellow',
                        },
                    ]}
                />

                <SecurityFilterBar onSubmit={apply} onClear={clear} columns={6}>
                    <FilterField label="Buscar">
                        <input
                            type="search"
                            placeholder="Usuario, correo o IP"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className={securityFieldClass}
                        />
                    </FilterField>
                    <FilterField label="Módulo">
                        <select
                            value={module}
                            onChange={(e) => setModule(e.target.value)}
                            className={securityFieldClass}
                        >
                            <option value="">Todos los módulos</option>
                            {catalog.modules.map((o) => (
                                <option key={o.value} value={o.value}>
                                    {o.label}
                                </option>
                            ))}
                        </select>
                    </FilterField>
                    <FilterField label="Acción">
                        <select
                            value={action}
                            onChange={(e) => setAction(e.target.value)}
                            className={securityFieldClass}
                        >
                            <option value="">Todas las acciones</option>
                            {catalog.actions.map((o) => (
                                <option key={o.value} value={o.value}>
                                    {o.label}
                                </option>
                            ))}
                        </select>
                    </FilterField>
                    <FilterField label="Severidad">
                        <select
                            value={severity}
                            onChange={(e) => setSeverity(e.target.value)}
                            className={securityFieldClass}
                        >
                            <option value="">Todas</option>
                            {catalog.severities.map((o) => (
                                <option key={o.value} value={o.value}>
                                    {o.label}
                                </option>
                            ))}
                        </select>
                    </FilterField>
                    <FilterField label="Desde">
                        <input
                            type="date"
                            value={date_from}
                            onChange={(e) => setDateFrom(e.target.value)}
                            className={securityFieldClass}
                        />
                    </FilterField>
                    <FilterField label="Hasta">
                        <input
                            type="date"
                            value={date_to}
                            onChange={(e) => setDateTo(e.target.value)}
                            className={securityFieldClass}
                        />
                    </FilterField>
                </SecurityFilterBar>

                <div className="grid gap-6 xl:grid-cols-12">
                    <div className="xl:col-span-4">
                        <AuditTimeline items={timeline} />
                    </div>
                    <div className="xl:col-span-8">
                        {rows.length === 0 ? (
                            <EmptyState
                                icon={ScrollText}
                                title="Sin registros"
                                description="Ajuste los filtros o espere nuevos eventos del sistema."
                            />
                        ) : (
                            <>
                                <div className="space-y-3 md:hidden">
                                    {rows.map((row) => (
                                        <AuditLogMobileCard key={row.id} row={row} />
                                    ))}
                                </div>
                                <div className="hidden md:block">
                                    <SecurityTableScroll
                                        title="Registro completo"
                                        description="Historial detallado de eventos auditados."
                                        footer={
                                            <SecurityPagination
                                                links={logs.links}
                                                total={logs.meta?.total}
                                            />
                                        }
                                    >
                                        <table className="min-w-full divide-y divide-plomo/10">
                                            <thead className="bg-navy-50/50">
                                                <tr>
                                                    <th className={securityThClass}>Fecha y hora</th>
                                                    <th className={securityThClass}>Usuario</th>
                                                    <th className={securityThClass}>Acción</th>
                                                    <th className={securityThClass}>Módulo</th>
                                                    <th className={securityThClass}>Origen</th>
                                                    <th className={securityThClass}>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-plomo/10 bg-white">
                                                {rows.map((row) => (
                                                    <tr
                                                        key={row.id}
                                                        className="transition hover:bg-navy-50/30"
                                                    >
                                                        <td className={`${securityTdClass} whitespace-nowrap`}>
                                                            <span className="font-medium">
                                                                {row.created_at_label}
                                                            </span>
                                                        </td>
                                                        <td className={securityTdClass}>
                                                            <SecurityUserCell
                                                                name={row.user?.name}
                                                                email={row.user?.email}
                                                                role={row.user_role}
                                                            />
                                                        </td>
                                                        <td className={securityTdClass}>
                                                            <AuditActionBadge
                                                                action={row.action}
                                                                label={row.action_label}
                                                            />
                                                        </td>
                                                        <td className={securityTdClass}>
                                                            <span className="inline-flex rounded-md bg-navy-50 px-2 py-1 text-xs font-medium text-navy-800">
                                                                {row.module_label}
                                                            </span>
                                                        </td>
                                                        <td className={securityTdClass}>
                                                            <SecurityNetworkCell
                                                                ip={row.ip_address}
                                                                browser={row.browser}
                                                            />
                                                        </td>
                                                        <td className={securityTdClass}>
                                                            <div className="flex flex-col gap-1.5">
                                                                <AuditSeverityBadge
                                                                    severity={row.severity}
                                                                />
                                                                <AuditResultBadge result={row.result} />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </SecurityTableScroll>
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </PageContainer>
        </IntranetLayout>
    );
}
