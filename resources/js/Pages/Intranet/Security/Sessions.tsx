import SessionRiskBadge from '@/Components/Security/SessionRiskBadge';
import SecurityFlash from '@/Components/Security/SecurityFlash';
import SecurityNetworkCell from '@/Components/Security/SecurityNetworkCell';
import SecurityPagination from '@/Components/Security/SecurityPagination';
import SecuritySummaryKpis from '@/Components/Security/SecuritySummaryKpis';
import SecurityTableScroll from '@/Components/Security/SecurityTableScroll';
import SecurityUserCell from '@/Components/Security/SecurityUserCell';
import { securityTdClass, securityThClass } from '@/Components/Security/securityUi';
import { EmptyState } from '@/Components/Intranet/EmptyState';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { AlertTriangle, Clock, Monitor, ShieldAlert } from 'lucide-react';

type SessionRow = {
    id: number;
    user?: { name: string; email: string };
    ip_address: string | null;
    user_agent?: string | null;
    device_label: string | null;
    is_suspicious: boolean;
    logged_in_at: string | null;
    last_activity_at: string | null;
    expires_at: string | null;
};

type P = PageProps<{
    stats: { active: number; suspicious: number; expiring_soon: number };
    sessions: {
        data: SessionRow[];
        links?: { url: string | null; label: string; active: boolean }[];
        meta?: { total: number };
    };
}>;

export default function SecuritySessions() {
    const { sessions, stats, flash } = usePage<P>().props;
    const rows = sessions.data ?? [];

    const revoke = (id: number) => {
        if (!confirm('¿Cerrar esta sesión de forma inmediata?')) return;
        router.post(route('intranet.security.sessions.revoke', id));
    };

    return (
        <IntranetLayout title="Sesiones activas">
            <Head title="Sesiones — Horizonte" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Seguridad y auditoría' },
                        { label: 'Sesiones activas' },
                    ]}
                />
                {flash?.success ? <SecurityFlash message={flash.success} /> : null}
                <SectionTitle
                    title="Sesiones activas"
                    description="Monitoreo de dispositivos con sesión vigente. Revoque accesos sospechosos o innecesarios."
                />

                <SecuritySummaryKpis
                    items={[
                        { title: 'Sesiones activas', value: stats.active, icon: Monitor },
                        {
                            title: 'Sospechosas',
                            value: stats.suspicious,
                            icon: ShieldAlert,
                            accent: stats.suspicious > 0 ? 'red' : 'navy',
                        },
                        {
                            title: 'Por expirar (30 min)',
                            value: stats.expiring_soon,
                            icon: Clock,
                            accent: stats.expiring_soon > 0 ? 'yellow' : 'navy',
                        },
                        {
                            title: 'Listadas',
                            value: sessions.meta?.total ?? rows.length,
                            icon: AlertTriangle,
                            accent: 'navy',
                        },
                    ]}
                />

                {rows.length === 0 ? (
                    <EmptyState
                        icon={Monitor}
                        title="No hay sesiones activas"
                        description="Cuando los usuarios inicien sesión, aparecerán aquí."
                    />
                ) : (
                    <SecurityTableScroll
                        title="Dispositivos conectados"
                        description="Última actividad y vencimiento de cada sesión."
                        footer={
                            <SecurityPagination
                                links={sessions.links}
                                total={sessions.meta?.total}
                            />
                        }
                    >
                        <table className="min-w-full divide-y divide-plomo/10">
                            <thead className="bg-navy-50/50">
                                <tr>
                                    <th className={securityThClass}>Usuario</th>
                                    <th className={securityThClass}>Dispositivo</th>
                                    <th className={securityThClass}>Origen</th>
                                    <th className={securityThClass}>Inicio</th>
                                    <th className={securityThClass}>Última actividad</th>
                                    <th className={securityThClass}>Expira</th>
                                    <th className={`${securityThClass} text-right`}>Acción</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-plomo/10">
                                {rows.map((row) => (
                                    <tr key={row.id} className="hover:bg-navy-50/30">
                                        <td className={securityTdClass}>
                                            <SecurityUserCell
                                                name={row.user?.name}
                                                email={row.user?.email}
                                            />
                                        </td>
                                        <td className={securityTdClass}>
                                            <p className="font-medium">{row.device_label ?? '—'}</p>
                                            <SessionRiskBadge suspicious={row.is_suspicious} />
                                        </td>
                                        <td className={securityTdClass}>
                                            <SecurityNetworkCell
                                                ip={row.ip_address}
                                                browser={row.device_label}
                                            />
                                        </td>
                                        <td className={`${securityTdClass} whitespace-nowrap text-plomo`}>
                                            {row.logged_in_at}
                                        </td>
                                        <td className={`${securityTdClass} whitespace-nowrap font-medium`}>
                                            {row.last_activity_at}
                                        </td>
                                        <td className={`${securityTdClass} whitespace-nowrap text-plomo`}>
                                            {row.expires_at}
                                        </td>
                                        <td className={`${securityTdClass} text-right`}>
                                            <button
                                                type="button"
                                                onClick={() => revoke(row.id)}
                                                className="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-800 hover:bg-rose-100"
                                            >
                                                Cerrar sesión
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </SecurityTableScroll>
                )}
            </PageContainer>
        </IntranetLayout>
    );
}
