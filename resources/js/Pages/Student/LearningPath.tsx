import StudentPortalEmpty from '@/Components/Student/StudentPortalEmpty';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type PortalCtx = {
    student: { id: number; full_name: string; code: string } | null;
    has_student: boolean;
    portal_scoped: boolean;
    empty_message: string;
};

type Profile = {
    last_classified_level: string | null;
    last_diagnostic_score: number | null;
    weakness_topics: string[];
    learning_path: { topic: string; status: string; percent?: number }[];
    last_diagnostic_at: string | null;
} | null;

type Rec = {
    id: number;
    title: string;
    body: string;
    topic: string | null;
    priority: number;
    source: string;
    estimated_weeks_to_improve: number | null;
};

type Props = PageProps<{
    portal: PortalCtx;
    profile: Profile;
    recommendations: Rec[];
}>;

export default function LearningPath() {
    const { portal, profile, recommendations } = usePage<Props>().props;

    return (
        <StudentLayout title="Ruta de aprendizaje">
            <Head title="Ruta de aprendizaje" />
            <PageContainer>
                <SectionTitle
                    title="Progreso y recomendaciones"
                    description="Seguimiento local: diagnóstico, hábitos y temas a reforzar. Sin proveedor de IA requerido."
                />
                {!portal.has_student ? (
                    <StudentPortalEmpty message={portal.empty_message} portalScoped={portal.portal_scoped} />
                ) : (
                    <div className="grid gap-6 lg:grid-cols-2">
                        <Card>
                            <h2 className="text-lg font-bold text-navy-900">Perfil adaptativo</h2>
                            {profile ? (
                                <ul className="mt-3 space-y-2 text-sm text-plomo">
                                    <li>
                                        Nivel:{' '}
                                        <strong className="capitalize text-navy-900">
                                            {profile.last_classified_level ?? '—'}
                                        </strong>
                                    </li>
                                    <li>
                                        Último puntaje diagnóstico:{' '}
                                        <strong>{profile.last_diagnostic_score ?? '—'}%</strong>
                                    </li>
                                </ul>
                            ) : (
                                <p className="mt-2 text-sm text-plomo">Aún no registramos un diagnóstico.</p>
                            )}
                            <div className="mt-4 space-y-2">
                                <p className="text-xs font-semibold uppercase text-plomo">Ruta sugerida</p>
                                {(profile?.learning_path ?? []).length === 0 ? (
                                    <p className="text-sm text-plomo">Sin metas listadas.</p>
                                ) : (
                                    <ul className="space-y-2">
                                        {profile?.learning_path.map((s, i) => (
                                            <li
                                                key={i}
                                                className="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2 text-sm"
                                            >
                                                <span className="font-medium text-navy-900">{s.topic}</span>
                                                <span className="ml-2 text-plomo">
                                                    ({s.status}
                                                    {typeof s.percent === 'number' ? ` · ${s.percent}%` : ''})
                                                </span>
                                            </li>
                                        ))}
                                    </ul>
                                )}
                            </div>
                        </Card>
                        <Card>
                            <h2 className="text-lg font-bold text-navy-900">Recomendaciones</h2>
                            <ul className="mt-4 space-y-3">
                                {recommendations.length === 0 ? (
                                    <li className="text-sm text-plomo">Sin recomendaciones generadas.</li>
                                ) : (
                                    recommendations.map((r) => (
                                        <li key={r.id} className="rounded-xl border border-slate-100 p-3">
                                            <p className="font-semibold text-navy-900">{r.title}</p>
                                            <p className="mt-1 text-sm text-plomo">{r.body}</p>
                                            {r.estimated_weeks_to_improve != null && (
                                                <p className="mt-2 text-xs text-plomo">
                                                    Tiempo estimado de mejora: ~{r.estimated_weeks_to_improve} sem.
                                                </p>
                                            )}
                                        </li>
                                    ))
                                )}
                            </ul>
                        </Card>
                    </div>
                )}
            </PageContainer>
        </StudentLayout>
    );
}
