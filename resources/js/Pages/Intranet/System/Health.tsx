import AnalyticsKpiCard from '@/Components/Analytics/AnalyticsKpiCard';
import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { Activity, Database, HardDrive, Layers, Server } from 'lucide-react';
import {
    checkStatusBadgeClass,
    computeDiskFreePercent,
    healthStatusTone,
} from '@/Pages/Intranet/System/healthUtils';

function formatBytes(n: number | null | undefined): string {
    if (n == null || n <= 0) return '—';
    const u = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.min(u.length - 1, Math.floor(Math.log(n) / Math.log(1024)));
    return `${(n / 1024 ** i).toFixed(i ? 2 : 0)} ${u[i]}`;
}

type HealthPayload = {
    status: 'ok' | 'warning' | 'critical';
    app: { name: string; env: string; debug: boolean; url: string };
    database: {
        ok: boolean;
        latency_ms: number | null;
        error: string | null;
        connection: string;
    };
    queue: { driver: string; pending_jobs: number; failed_jobs: number };
    storage: {
        disk_free_bytes: number | null;
        disk_total_bytes: number | null;
        disk_usage_percent: number | null;
        is_storage_writable: boolean;
        is_bootstrap_cache_writable: boolean;
        public_storage_linked: boolean;
    };
    cache: { driver: string; writable: boolean };
    scheduler: { timezone: string; note: string; last_heartbeat: string | null };
    mail: { mailer: string; host: string | null; port: number | null; configured: boolean };
    backups: {
        folder_exists: boolean;
        count: number;
        latest_name: string | null;
        latest_size_bytes: number | null;
        latest_modified_at: string | null;
    };
    checks: Record<
        string,
        {
            label: string;
            status: 'ok' | 'warning' | 'critical';
            value: string | null;
            message: string;
        }
    >;
    generated_at: string;
};

type P = PageProps<{
    health: HealthPayload;
    metrics_snapshot: Record<string, unknown> | null;
    env_issues: string[];
    backups_count: number;
    recent_errors: string[];
}>;

export default function SystemHealth() {
    const { health, metrics_snapshot, env_issues, backups_count, recent_errors } = usePage<P>().props;

    const statusTone = healthStatusTone(health.status);

    const diskPct = computeDiskFreePercent(
        health.storage.disk_free_bytes,
        health.storage.disk_total_bytes,
    );

    return (
        <IntranetLayout title="Salud del sistema">
            <Head title="Salud del sistema — Horizonte" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[{ label: 'Administración' }, { label: 'Salud del sistema' }]}
                />
                <SectionTitle
                    title="Salud del sistema"
                    description="Estado operativo y hardening de producción: app, DB, colas, scheduler, storage, backups y correo."
                />

                <div className={`mb-6 rounded-xl border px-4 py-3 text-sm ${statusTone}`}>
                    Estado general de producción: <span className="font-semibold uppercase">{health.status}</span>
                </div>

                {env_issues.length > 0 ? (
                    <div className="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950">
                        <p className="font-semibold">Variables de entorno</p>
                        <ul className="mt-2 list-inside list-disc space-y-1">
                            {env_issues.map((m) => (
                                <li key={m}>{m}</li>
                            ))}
                        </ul>
                    </div>
                ) : null}

                <div className="mb-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <AnalyticsKpiCard
                        title="Base de datos"
                        value={health.database.ok ? 'Operativa' : 'Error'}
                        subtitle={
                            health.database.ok
                                ? `${health.database.latency_ms ?? '—'} ms · ${health.database.connection}`
                                : health.database.error ?? ''
                        }
                        icon={Database}
                        accent={health.database.ok ? 'navy' : 'red'}
                    />
                    <AnalyticsKpiCard
                        title="Cola"
                        value={health.queue.driver}
                        subtitle={`Pendientes: ${health.queue.pending_jobs} · Fallidos: ${health.queue.failed_jobs}`}
                        icon={Layers}
                        accent={health.queue.failed_jobs > 0 ? 'red' : 'navy'}
                    />
                    <AnalyticsKpiCard
                        title="Almacenamiento libre"
                        value={formatBytes(health.storage.disk_free_bytes)}
                        subtitle={
                            diskPct != null
                                ? `${diskPct}% libre · Total ${formatBytes(health.storage.disk_total_bytes)}`
                                : undefined
                        }
                        icon={HardDrive}
                        accent={diskPct != null && diskPct < 10 ? 'red' : 'navy'}
                    />
                    <AnalyticsKpiCard
                        title="Respaldos locales"
                        value={backups_count}
                        subtitle="Archivos en storage/app/backups"
                        icon={Server}
                        accent="yellow"
                    />
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <h3 className="mb-3 text-sm font-bold text-navy-900">Aplicación</h3>
                        <dl className="space-y-2 text-sm">
                            <div className="flex justify-between gap-4 border-b border-plomo/10 py-2">
                                <dt className="text-plomo">Nombre</dt>
                                <dd className="font-medium text-navy-900">{health.app.name}</dd>
                            </div>
                            <div className="flex justify-between gap-4 border-b border-plomo/10 py-2">
                                <dt className="text-plomo">Entorno</dt>
                                <dd className="font-medium text-navy-900">{health.app.env}</dd>
                            </div>
                            <div className="flex justify-between gap-4 border-b border-plomo/10 py-2">
                                <dt className="text-plomo">Debug</dt>
                                <dd className="font-medium text-navy-900">
                                    {health.app.debug ? 'activado' : 'desactivado'}
                                </dd>
                            </div>
                            <div className="flex justify-between gap-4 py-2">
                                <dt className="text-plomo">Caché</dt>
                                <dd className="font-medium text-navy-900">
                                    {health.cache.driver} —{' '}
                                    {health.cache.writable ? 'escribible' : 'no escribible'}
                                </dd>
                            </div>
                            <div className="flex justify-between gap-4 py-2">
                                <dt className="text-plomo">Storage link</dt>
                                <dd className="font-medium text-navy-900">
                                    {health.storage.public_storage_linked ? 'activo' : 'faltante'}
                                </dd>
                            </div>
                        </dl>
                    </Card>
                    <Card>
                        <h3 className="mb-3 flex items-center gap-2 text-sm font-bold text-navy-900">
                            <Activity className="h-4 w-4 text-celeste" />
                            Programador (scheduler)
                        </h3>
                        <p className="text-sm text-plomo">{health.scheduler.note}</p>
                        <p className="mt-2 text-xs text-plomo">
                            Zona horaria: {health.scheduler.timezone}
                        </p>
                        <p className="mt-2 text-xs text-plomo">
                            Último heartbeat:{' '}
                            {health.scheduler.last_heartbeat
                                ? new Date(health.scheduler.last_heartbeat).toLocaleString()
                                : 'no detectado'}
                        </p>
                        <p className="mt-4 text-xs text-plomo">
                            Generado: {new Date(health.generated_at).toLocaleString()}
                        </p>
                    </Card>
                </div>

                <Card className="mt-6">
                    <h3 className="mb-3 text-sm font-bold text-navy-900">
                        Checks de readiness (ok / warning / critical)
                    </h3>
                    <div className="grid gap-3 md:grid-cols-2">
                        {Object.entries(health.checks).map(([key, check]) => (
                            <div
                                key={key}
                                className="rounded-lg border border-plomo/15 bg-white/80 px-3 py-2 text-sm"
                            >
                                <div className="flex items-center justify-between gap-3">
                                    <p className="font-semibold text-navy-900">{check.label}</p>
                                    <span
                                        className={`rounded-full px-2 py-0.5 text-xs font-semibold uppercase ${checkStatusBadgeClass(check.status)}`}
                                    >
                                        {check.status}
                                    </span>
                                </div>
                                <p className="mt-1 text-xs text-plomo">{check.message}</p>
                                {check.value ? (
                                    <p className="mt-1 text-xs text-navy-800">{check.value}</p>
                                ) : null}
                            </div>
                        ))}
                    </div>
                </Card>

                <Card className="mt-6">
                    <h3 className="mb-3 text-sm font-bold text-navy-900">Errores recientes</h3>
                    {recent_errors.length > 0 ? (
                        <pre className="max-h-56 overflow-auto rounded-lg bg-red-50 p-3 text-xs text-red-900">
                            {recent_errors.join('\n')}
                        </pre>
                    ) : (
                        <p className="text-sm text-plomo">No se detectaron errores recientes en el log principal.</p>
                    )}
                </Card>

                <Card className="mt-6">
                    <h3 className="mb-3 text-sm font-bold text-navy-900">
                        Snapshot métricas (IA / analítica futura)
                    </h3>
                    {metrics_snapshot ? (
                        <pre className="max-h-64 overflow-auto rounded-lg bg-navy-50 p-4 text-xs text-navy-900">
                            {JSON.stringify(metrics_snapshot, null, 2)}
                        </pre>
                    ) : (
                        <p className="text-sm text-plomo">
                            Aún no hay snapshot (se genera con el job programado o al ejecutar colas).
                        </p>
                    )}
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
