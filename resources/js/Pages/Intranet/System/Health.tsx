import AnalyticsKpiCard from '@/Components/Analytics/AnalyticsKpiCard';
import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { Activity, Database, HardDrive, Layers, Server } from 'lucide-react';

function formatBytes(n: number | null | undefined): string {
    if (n == null || n <= 0) return '—';
    const u = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.min(u.length - 1, Math.floor(Math.log(n) / Math.log(1024)));
    return `${(n / 1024 ** i).toFixed(i ? 2 : 0)} ${u[i]}`;
}

type HealthPayload = {
    app: { name: string; env: string; debug: boolean; url: string };
    database: {
        ok: boolean;
        latency_ms: number | null;
        error: string | null;
        connection: string;
    };
    queue: { driver: string; pending_jobs: number; failed_jobs: number };
    storage: { disk_free_bytes: number | null; disk_total_bytes: number | null };
    cache: { driver: string; writable: boolean };
    scheduler: { timezone: string; note: string };
    generated_at: string;
};

type P = PageProps<{
    health: HealthPayload;
    metrics_snapshot: Record<string, unknown> | null;
    env_issues: string[];
    backups_count: number;
}>;

export default function SystemHealth() {
    const { health, metrics_snapshot, env_issues, backups_count } = usePage<P>().props;

    const diskPct =
        health.storage.disk_free_bytes != null &&
        health.storage.disk_total_bytes != null &&
        health.storage.disk_total_bytes > 0
            ? Math.round(
                  (health.storage.disk_free_bytes / health.storage.disk_total_bytes) * 100,
              )
            : null;

    return (
        <IntranetLayout title="Salud del sistema">
            <Head title="Salud del sistema — Horizonte" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[{ label: 'Administración' }, { label: 'Salud del sistema' }]}
                />
                <SectionTitle
                    title="Salud del sistema"
                    description="Estado de base de datos, colas, almacenamiento y caché. Panel operativo para despliegue ISO."
                />

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
                        <p className="mt-4 text-xs text-plomo">
                            Generado: {new Date(health.generated_at).toLocaleString()}
                        </p>
                    </Card>
                </div>

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
