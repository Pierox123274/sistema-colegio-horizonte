import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { RefreshCw, Sparkles } from 'lucide-react';

type Payload = {
    overview: {
        total_students: number;
        by_level: { high: number; medium: number; low: number };
        high_risk_samples: Array<{
            student: { id: number; code: string; full_name: string };
            risk: { level: string; score: number };
        }>;
    };
    ai_summary: string | null;
};

type Usage = {
    period_days: number;
    total_queries: number;
    success_rate: number | null;
    cache_hit_rate: number | null;
    by_action: Record<string, number>;
    modules: Record<string, boolean>;
};

type Props = PageProps<{
    payload: Payload;
    usage: Usage;
    ai_enabled: boolean;
    provider: string;
    modules: Record<string, boolean>;
}>;

export default function AIAnalyticsIndex() {
    const { payload, usage, ai_enabled, provider } = usePage<Props>().props;

    const refresh = () => {
        router.post(route('intranet.ai-analytics.refresh'), {}, { preserveScroll: true });
    };

    return (
        <IntranetLayout title="IA institucional">
            <Head title="IA institucional" />
            <PageContainer>
                <SectionTitle
                    title="Analítica predictiva IA"
                    description={`Proveedor configurado: ${provider}. Las cifras provienen de registros académicos locales; el texto IA es orientativo.`}
                    actions={
                        <button
                            type="button"
                            onClick={refresh}
                            className="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-navy shadow-sm hover:bg-slate-50"
                        >
                            <RefreshCw className="h-4 w-4" /> Regenerar (cola)
                        </button>
                    }
                />

                <div className="mb-6 grid gap-4 md:grid-cols-4">
                    <Card>
                        <p className="text-xs uppercase text-plomo">Matriculados (año activo)</p>
                        <p className="text-3xl font-bold text-navy">{payload.overview.total_students}</p>
                    </Card>
                    <Card className="border-l-4 border-l-red-500">
                        <p className="text-xs uppercase text-plomo">Riesgo alto</p>
                        <p className="text-3xl font-bold text-red-700">{payload.overview.by_level.high}</p>
                    </Card>
                    <Card className="border-l-4 border-l-amber-500">
                        <p className="text-xs uppercase text-plomo">Riesgo medio</p>
                        <p className="text-3xl font-bold text-amber-800">{payload.overview.by_level.medium}</p>
                    </Card>
                    <Card className="border-l-4 border-l-emerald-500">
                        <p className="text-xs uppercase text-plomo">Riesgo bajo</p>
                        <p className="text-3xl font-bold text-emerald-800">{payload.overview.by_level.low}</p>
                    </Card>
                </div>

                {payload.ai_summary && (
                    <Card className="mb-6 border-l-4 border-l-brand-yellow">
                        <h3 className="mb-2 flex items-center gap-2 text-sm font-semibold text-navy">
                            <Sparkles className="h-4 w-4 text-brand-yellow" /> Narrativa IA
                            {!ai_enabled && (
                                <span className="text-xs font-normal text-plomo">
                                    (IA deshabilitada; texto puede provenir de caché)
                                </span>
                            )}
                        </h3>
                        <p className="whitespace-pre-wrap text-sm text-slate-700">{payload.ai_summary}</p>
                    </Card>
                )}

                <Card className="mb-6">
                    <h3 className="mb-3 text-sm font-semibold text-navy">Uso IA (últimos {usage.period_days} días)</h3>
                    <div className="grid gap-4 sm:grid-cols-3 text-sm">
                        <div>
                            <p className="text-xs uppercase text-plomo">Consultas</p>
                            <p className="text-2xl font-bold text-navy">{usage.total_queries}</p>
                        </div>
                        <div>
                            <p className="text-xs uppercase text-plomo">Éxito</p>
                            <p className="text-2xl font-bold text-emerald-700">
                                {usage.success_rate ?? '—'}%
                            </p>
                        </div>
                        <div>
                            <p className="text-xs uppercase text-plomo">Caché</p>
                            <p className="text-2xl font-bold text-slate-700">
                                {usage.cache_hit_rate ?? '—'}%
                            </p>
                        </div>
                    </div>
                    {Object.keys(usage.by_action).length > 0 && (
                        <ul className="mt-4 divide-y text-xs text-plomo">
                            {Object.entries(usage.by_action).map(([action, total]) => (
                                <li key={action} className="flex justify-between py-1">
                                    <span>{action}</span>
                                    <span className="font-semibold text-navy">{total}</span>
                                </li>
                            ))}
                        </ul>
                    )}
                </Card>

                <Card>
                    <h3 className="mb-3 text-sm font-semibold text-navy">Muestra de estudiantes en riesgo alto</h3>
                    <ul className="divide-y divide-slate-100 text-sm">
                        {payload.overview.high_risk_samples.length === 0 ? (
                            <li className="py-4 text-plomo">Sin casos en este momento o sin año activo.</li>
                        ) : (
                            payload.overview.high_risk_samples.map((row) => (
                                <li key={row.student.id} className="flex justify-between py-2">
                                    <span>
                                        {row.student.full_name}
                                        <span className="ml-2 text-xs text-plomo">{row.student.code}</span>
                                    </span>
                                    <span className="text-xs font-semibold text-red-700">
                                        puntos {row.risk.score}
                                    </span>
                                </li>
                            ))
                        )}
                    </ul>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
