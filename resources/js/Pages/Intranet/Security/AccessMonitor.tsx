import AuditTimeline, { type AuditTimelineItem } from '@/Components/Security/AuditTimeline';
import LoginAttemptStatusBadge from '@/Components/Security/LoginAttemptStatusBadge';
import SecurityNetworkCell from '@/Components/Security/SecurityNetworkCell';
import SecuritySummaryKpis from '@/Components/Security/SecuritySummaryKpis';
import SecurityTableScroll from '@/Components/Security/SecurityTableScroll';
import SecurityUserCell from '@/Components/Security/SecurityUserCell';
import { securityTdClass, securityThClass } from '@/Components/Security/securityUi';
import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import {
    Activity,
    AlertTriangle,
    Calendar,
    Globe,
    Monitor,
    Shield,
    ShieldAlert,
} from 'lucide-react';

type P = PageProps<{
    summary: {
        failed_last_24h: number;
        successful_last_24h: number;
        suspicious_ips: string[];
        active_sessions: number;
        events_today: number;
        critical_events: number;
    };
    recent_accesses: {
        id: number;
        email: string;
        user?: { name: string; email: string };
        ip_address: string | null;
        successful: boolean;
        failure_reason?: string | null;
        attempted_at_label: string | null;
    }[];
    recent_audit: AuditTimelineItem[];
}>;

export default function SecurityAccessMonitor() {
    const { summary, recent_accesses, recent_audit } = usePage<P>().props;

    return (
        <IntranetLayout title="Monitoreo de accesos">
            <Head title="Accesos — Horizonte" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Seguridad y auditoría' },
                        { label: 'Accesos recientes' },
                    ]}
                />
                <SectionTitle
                    title="Monitoreo de accesos"
                    description="Panel operativo ISO: actividad de las últimas 24 horas y señales de riesgo."
                />

                <SecuritySummaryKpis
                    items={[
                        {
                            title: 'Accesos exitosos (24h)',
                            value: summary.successful_last_24h,
                            icon: Shield,
                        },
                        {
                            title: 'Intentos fallidos (24h)',
                            value: summary.failed_last_24h,
                            icon: AlertTriangle,
                            accent: summary.failed_last_24h > 0 ? 'red' : 'navy',
                        },
                        {
                            title: 'Sesiones activas',
                            value: summary.active_sessions,
                            icon: Monitor,
                        },
                        {
                            title: 'Eventos hoy',
                            value: summary.events_today,
                            icon: Calendar,
                        },
                        {
                            title: 'Eventos críticos (24h)',
                            value: summary.critical_events,
                            icon: ShieldAlert,
                            accent: summary.critical_events > 0 ? 'red' : 'navy',
                        },
                        {
                            title: 'IPs sospechosas',
                            value: summary.suspicious_ips.length,
                            icon: Activity,
                            accent: summary.suspicious_ips.length > 0 ? 'red' : 'navy',
                        },
                    ]}
                />

                {summary.suspicious_ips.length > 0 ? (
                    <Card className="mb-6 border-amber-200 bg-amber-50/80">
                        <p className="text-sm font-semibold text-amber-950">
                            IPs con actividad anómala detectada
                        </p>
                        <div className="mt-2 flex flex-wrap gap-2">
                            {summary.suspicious_ips.map((ip) => (
                                <span
                                    key={ip}
                                    className="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-1 font-mono text-xs text-amber-900 ring-1 ring-amber-200"
                                >
                                    <Globe className="h-3.5 w-3.5" aria-hidden />
                                    {ip}
                                </span>
                            ))}
                        </div>
                    </Card>
                ) : null}

                <div className="grid gap-6 xl:grid-cols-2">
                    <SecurityTableScroll
                        title="Accesos recientes"
                        description="Últimos intentos de inicio de sesión."
                    >
                        <table className="min-w-full divide-y divide-plomo/10">
                            <thead className="bg-navy-50/50">
                                <tr>
                                    <th className={securityThClass}>Fecha</th>
                                    <th className={securityThClass}>Cuenta</th>
                                    <th className={securityThClass}>Origen</th>
                                    <th className={securityThClass}>Estado</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-plomo/10">
                                {recent_accesses.map((row) => (
                                    <tr key={row.id} className="hover:bg-navy-50/30">
                                        <td className={`${securityTdClass} whitespace-nowrap`}>
                                            {row.attempted_at_label}
                                        </td>
                                        <td className={securityTdClass}>
                                            <SecurityUserCell
                                                name={row.user?.name ?? row.email}
                                                email={row.user?.email ?? row.email}
                                            />
                                        </td>
                                        <td className={securityTdClass}>
                                            <SecurityNetworkCell ip={row.ip_address} />
                                        </td>
                                        <td className={securityTdClass}>
                                            <LoginAttemptStatusBadge
                                                successful={row.successful}
                                                failureReason={row.failure_reason}
                                            />
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </SecurityTableScroll>

                    <AuditTimeline items={recent_audit} title="Auditoría reciente" />
                </div>
            </PageContainer>
        </IntranetLayout>
    );
}
