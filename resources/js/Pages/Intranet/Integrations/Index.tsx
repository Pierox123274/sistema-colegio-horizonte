import IntegrationHealthBadge from '@/Components/Integrations/IntegrationHealthBadge';
import ProviderCard from '@/Components/Integrations/ProviderCard';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { Mail, Webhook } from 'lucide-react';

type Provider = {
    key: string;
    label: string;
    provider: string;
    configured: boolean;
    description: string;
    status: string;
};

type HealthCheck = {
    label: string;
    status: string;
    value?: unknown;
    message: string;
};

type Props = PageProps<{
    providers: Provider[];
    health: { status: string; checks: Record<string, HealthCheck> };
    mail: {
        mailer: string;
        configured: boolean;
        queue_pending: number;
        failed: number;
    };
    storage: { disk: string; driver: string; external_enabled: boolean };
    recent_webhooks: Array<{
        id: number;
        provider: string;
        event_type: string | null;
        status: string;
        signature_valid: string | null;
        created_at: string;
    }>;
    recent_emails: Array<{
        id: number;
        mailable_class: string;
        status: string;
        attempts: number;
        created_at: string;
    }>;
    integrations_enabled: boolean;
}>;

export default function IntegrationsIndex() {
    const { providers, health, mail, storage, recent_webhooks, recent_emails, integrations_enabled } =
        usePage<Props>().props;

    return (
        <IntranetLayout title="Integraciones">
            <Head title="Integraciones externas" />
            <PageContainer>
                <SectionTitle
                    title="Integraciones institucionales"
                    description="Estado de proveedores externos, webhooks y correo. Los secretos solo viven en variables de entorno."
                />

                <div className="mb-6 flex flex-wrap items-center gap-2">
                    <IntegrationHealthBadge status={health.status} label="Global" />
                    {!integrations_enabled && (
                        <span className="text-xs text-amber-700">INTEGRATIONS_ENABLED=false</span>
                    )}
                </div>

                <div className="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {providers.map((p) => (
                        <ProviderCard
                            key={p.key}
                            label={p.label}
                            provider={p.provider}
                            description={p.description}
                            configured={p.configured}
                            status={p.status}
                        />
                    ))}
                </div>

                <div className="mb-8 grid gap-4 lg:grid-cols-2">
                    <Card>
                        <h3 className="mb-3 flex items-center gap-2 text-sm font-semibold text-navy">
                            <Mail className="h-4 w-4" /> Correo (SMTP)
                        </h3>
                        <ul className="space-y-1 text-sm text-slate-700">
                            <li>Mailer: {mail.mailer}</li>
                            <li>Configurado: {mail.configured ? 'Sí' : 'No'}</li>
                            <li>Cola pendiente: {mail.queue_pending}</li>
                            <li>Fallidos: {mail.failed}</li>
                        </ul>
                    </Card>
                    <Card>
                        <h3 className="mb-3 text-sm font-semibold text-navy">Almacenamiento</h3>
                        <ul className="space-y-1 text-sm text-slate-700">
                            <li>Disco: {storage.disk}</li>
                            <li>Driver: {storage.driver}</li>
                            <li>Externo habilitado: {storage.external_enabled ? 'Sí' : 'No'}</li>
                        </ul>
                    </Card>
                </div>

                <Card className="mb-6">
                    <h3 className="mb-3 text-sm font-semibold text-navy">Health checks</h3>
                    <ul className="divide-y text-sm">
                        {Object.entries(health.checks).map(([key, check]) => (
                            <li key={key} className="flex flex-wrap items-center justify-between gap-2 py-2">
                                <span className="font-medium text-navy">{check.label}</span>
                                <IntegrationHealthBadge status={check.status} label="" />
                                <span className="w-full text-xs text-plomo">{check.message}</span>
                            </li>
                        ))}
                    </ul>
                </Card>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <h3 className="mb-3 flex items-center gap-2 text-sm font-semibold text-navy">
                            <Webhook className="h-4 w-4" /> Webhooks recientes
                        </h3>
                        <ul className="divide-y text-xs">
                            {recent_webhooks.length === 0 ? (
                                <li className="py-3 text-plomo">Sin registros.</li>
                            ) : (
                                recent_webhooks.map((w) => (
                                    <li key={w.id} className="flex items-center justify-between py-2">
                                        <span>
                                            {w.provider} · {w.event_type ?? '—'}
                                        </span>
                                        <span className="flex items-center gap-2">
                                            <span className="text-plomo">{w.status}</span>
                                            {w.status === 'failed' && (
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        router.post(
                                                            route('intranet.integrations.webhooks.replay', w.id),
                                                            {},
                                                            { preserveScroll: true },
                                                        )
                                                    }
                                                    className="text-brand-yellow hover:underline"
                                                >
                                                    Replay
                                                </button>
                                            )}
                                        </span>
                                    </li>
                                ))
                            )}
                        </ul>
                    </Card>
                    <Card>
                        <h3 className="mb-3 text-sm font-semibold text-navy">Envíos de correo</h3>
                        <ul className="divide-y text-xs">
                            {recent_emails.length === 0 ? (
                                <li className="py-3 text-plomo">Sin registros.</li>
                            ) : (
                                recent_emails.map((e) => (
                                    <li key={e.id} className="py-2">
                                        <p className="truncate font-mono text-[10px]">{e.mailable_class}</p>
                                        <p className="text-plomo">
                                            {e.status} · intentos {e.attempts}
                                        </p>
                                    </li>
                                ))
                            )}
                        </ul>
                    </Card>
                </div>
            </PageContainer>
        </IntranetLayout>
    );
}
