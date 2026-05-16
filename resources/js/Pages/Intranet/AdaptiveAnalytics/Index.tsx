import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type Props = PageProps<{
    overview: {
        attempt_count: number;
        avg_score: number | null;
        by_level: Record<string, number>;
        weak_topic_hits: Record<string, number>;
    };
}>;

export default function AdaptiveAnalyticsIndex() {
    const { overview } = usePage<Props>().props;

    return (
        <IntranetLayout title="Aprendizaje adaptativo">
            <Head title="Analítica adaptativa" />
            <PageContainer>
                <SectionTitle
                    title="Institución — aprendizaje adaptativo"
                    description="Agregados de diagnósticos y brechas temáticas sin servicios externos."
                />
                <div className="mb-6 grid gap-4 md:grid-cols-3">
                    <Card>
                        <p className="text-xs font-semibold uppercase text-plomo">Intentos completados</p>
                        <p className="mt-1 text-2xl font-bold text-navy-900">{overview.attempt_count}</p>
                    </Card>
                    <Card>
                        <p className="text-xs font-semibold uppercase text-plomo">Puntaje medio</p>
                        <p className="mt-1 text-2xl font-bold text-navy-900">
                            {overview.avg_score ?? '—'}
                        </p>
                    </Card>
                    <Card>
                        <p className="text-xs font-semibold uppercase text-plomo">Por nivel</p>
                        <ul className="mt-2 text-sm text-plomo">
                            {Object.entries(overview.by_level).map(([k, v]) => (
                                <li key={k} className="flex justify-between">
                                    <span className="capitalize">{k}</span>
                                    <span>{v}</span>
                                </li>
                            ))}
                        </ul>
                    </Card>
                </div>
                <Card>
                    <h2 className="font-bold text-navy-900">Brechas temáticas (institucional)</h2>
                    <ul className="mt-3 space-y-1 text-sm">
                        {Object.keys(overview.weak_topic_hits).length === 0 ? (
                            <li className="text-plomo">Sin datos.</li>
                        ) : (
                            Object.entries(overview.weak_topic_hits).map(([t, n]) => (
                                <li key={t} className="flex justify-between">
                                    {t}
                                    <span className="font-semibold">{n}</span>
                                </li>
                            ))
                        )}
                    </ul>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
