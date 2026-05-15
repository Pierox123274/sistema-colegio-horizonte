import LoginAttemptStatusBadge from '@/Components/Security/LoginAttemptStatusBadge';
import SecurityFilterBar from '@/Components/Security/SecurityFilterBar';
import SecurityNetworkCell from '@/Components/Security/SecurityNetworkCell';
import SecurityPagination from '@/Components/Security/SecurityPagination';
import SecuritySummaryKpis from '@/Components/Security/SecuritySummaryKpis';
import SecurityTableScroll from '@/Components/Security/SecurityTableScroll';
import SecurityUserCell from '@/Components/Security/SecurityUserCell';
import { securityFieldClass, securityTdClass, securityThClass } from '@/Components/Security/securityUi';
import { EmptyState } from '@/Components/Intranet/EmptyState';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { AlertTriangle, CheckCircle2, List, XCircle } from 'lucide-react';
import { FormEvent, useState, type ReactNode } from 'react';

type AttemptRow = {
    id: number;
    email: string;
    user?: { name: string; email?: string };
    ip_address: string | null;
    user_agent?: string | null;
    successful: boolean;
    failure_reason: string | null;
    attempted_at_label: string | null;
};

type P = PageProps<{
    filters: { email?: string; successful?: string; ip?: string };
    stats: {
        total: number;
        failed_today: number;
        successful_today: number;
        failed_24h: number;
    };
    attempts: {
        data: AttemptRow[];
        links?: { url: string | null; label: string; active: boolean }[];
        meta?: { total: number };
    };
}>;

function FilterField({ label, children }: { label: string; children: ReactNode }) {
    return (
        <label className="block text-sm md:col-span-1">
            <span className="mb-1 block text-xs font-semibold uppercase tracking-wide text-plomo">
                {label}
            </span>
            {children}
        </label>
    );
}

export default function SecurityLoginAttempts() {
    const { filters, stats, attempts } = usePage<P>().props;
    const [email, setEmail] = useState(String(filters.email ?? ''));
    const [successful, setSuccessful] = useState(String(filters.successful ?? ''));
    const [ip, setIp] = useState(String(filters.ip ?? ''));
    const rows = attempts.data ?? [];

    const apply = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('intranet.security.login-attempts.index'),
            {
                email: email || undefined,
                successful: successful || undefined,
                ip: ip || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const clear = () => {
        setEmail('');
        setSuccessful('');
        setIp('');
        router.get(route('intranet.security.login-attempts.index'), {}, { replace: true });
    };

    return (
        <IntranetLayout title="Intentos de acceso">
            <Head title="Intentos de login — Horizonte" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Seguridad y auditoría' },
                        { label: 'Intentos de acceso' },
                    ]}
                />
                <SectionTitle
                    title="Intentos de inicio de sesión"
                    description="Historial de accesos exitosos y fallidos para análisis de seguridad."
                />

                <SecuritySummaryKpis
                    items={[
                        { title: 'Total registrados', value: stats.total, icon: List },
                        {
                            title: 'Fallidos hoy',
                            value: stats.failed_today,
                            icon: XCircle,
                            accent: stats.failed_today > 0 ? 'red' : 'navy',
                        },
                        {
                            title: 'Exitosos hoy',
                            value: stats.successful_today,
                            icon: CheckCircle2,
                        },
                        {
                            title: 'Fallidos (24h)',
                            value: stats.failed_24h,
                            icon: AlertTriangle,
                            accent: stats.failed_24h > 0 ? 'red' : 'navy',
                        },
                    ]}
                />

                <SecurityFilterBar onSubmit={apply} onClear={clear} columns={4}>
                    <FilterField label="Correo">
                        <input
                            type="text"
                            placeholder="usuario@colegio.edu"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            className={securityFieldClass}
                        />
                    </FilterField>
                    <FilterField label="Resultado">
                        <select
                            value={successful}
                            onChange={(e) => setSuccessful(e.target.value)}
                            className={securityFieldClass}
                        >
                            <option value="">Todos los resultados</option>
                            <option value="1">Solo exitosos</option>
                            <option value="0">Solo fallidos</option>
                        </select>
                    </FilterField>
                    <FilterField label="Dirección IP">
                        <input
                            type="text"
                            placeholder="192.168.1.1"
                            value={ip}
                            onChange={(e) => setIp(e.target.value)}
                            className={securityFieldClass}
                        />
                    </FilterField>
                </SecurityFilterBar>

                {rows.length === 0 ? (
                    <EmptyState
                        icon={AlertTriangle}
                        title="Sin intentos"
                        description="No hay registros con los filtros actuales."
                    />
                ) : (
                    <SecurityTableScroll
                        title="Registro de intentos"
                        footer={
                            <SecurityPagination
                                links={attempts.links}
                                total={attempts.meta?.total}
                            />
                        }
                    >
                        <table className="min-w-full divide-y divide-plomo/10">
                            <thead className="bg-navy-50/50">
                                <tr>
                                    <th className={securityThClass}>Fecha y hora</th>
                                    <th className={securityThClass}>Cuenta</th>
                                    <th className={securityThClass}>Origen</th>
                                    <th className={securityThClass}>Resultado</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-plomo/10">
                                {rows.map((row) => (
                                    <tr key={row.id} className="hover:bg-navy-50/30">
                                        <td className={`${securityTdClass} whitespace-nowrap font-medium`}>
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
                )}
            </PageContainer>
        </IntranetLayout>
    );
}
