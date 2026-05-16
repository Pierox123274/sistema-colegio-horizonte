import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight, BarChart3, ClipboardList, Sparkles, TriangleAlert } from 'lucide-react';

type Row = { student_id: number; full_name: string; code: string; level: string | null; score: number | null };

type Props = PageProps<{
    low_students: Row[];
    weak_topics: Record<string, number>;
    students_without_diagnostic: number;
    ai_insights_href: string | null;
    analytics_href: string;
    diagnostics_href: string;
    academic_risk_href: string;
}>;

export default function PedagogicalPanel() {
    const {
        low_students,
        weak_topics,
        students_without_diagnostic,
        ai_insights_href,
        analytics_href,
        diagnostics_href,
        academic_risk_href,
    } = usePage<Props>().props;
    const topicEntries = Object.entries(weak_topics).slice(0, 8);

    return (
        <TeacherLayout title="Panel pedagógico">
            <Head title="Panel pedagógico" />
            <PageContainer>
                <SectionTitle
                    title="Panel pedagógico"
                    description="Resumen de diagnósticos, brechas temáticas y apoyos. Use Diagnósticos para gestionar exámenes y Riesgo académico para seguimiento priorizado."
                />

                <div className="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Card className="border-l-4 border-l-amber-500">
                        <p className="text-xs font-semibold uppercase text-plomo">Sin diagnóstico</p>
                        <p className="mt-1 text-3xl font-bold text-navy-900">{students_without_diagnostic}</p>
                        <Link
                            href={diagnostics_href}
                            className="mt-2 inline-flex items-center gap-1 text-sm font-medium text-brand-navy hover:underline"
                        >
                            Ir a diagnósticos
                            <ArrowRight className="h-4 w-4" />
                        </Link>
                    </Card>
                    <Card className="border-l-4 border-l-rose-500">
                        <p className="text-xs font-semibold uppercase text-plomo">Nivel básico / sin nivel</p>
                        <p className="mt-1 text-3xl font-bold text-navy-900">{low_students.length}</p>
                        <p className="mt-2 text-xs text-plomo">Muestra hasta 50 casos en la lista inferior.</p>
                    </Card>
                    <Card className="border-l-4 border-l-sky-500">
                        <p className="text-xs font-semibold uppercase text-plomo">Temas débiles (tipos)</p>
                        <p className="mt-1 text-3xl font-bold text-navy-900">{topicEntries.length}</p>
                    </Card>
                    <Card className="border-l-4 border-l-emerald-600">
                        <p className="text-xs font-semibold uppercase text-plomo">Accesos rápidos</p>
                        <div className="mt-3 flex flex-col gap-2 text-sm">
                            <Link href={diagnostics_href} className="font-medium text-brand-navy hover:underline">
                                Diagnósticos
                            </Link>
                            <Link href={academic_risk_href} className="font-medium text-brand-navy hover:underline">
                                Riesgo académico
                            </Link>
                            <Link href={analytics_href} className="inline-flex items-center gap-1 font-medium text-brand-navy hover:underline">
                                <BarChart3 className="h-4 w-4" />
                                Analítica
                            </Link>
                            {ai_insights_href ? (
                                <Link
                                    href={ai_insights_href}
                                    className="inline-flex items-center gap-1 font-medium text-brand-navy hover:underline"
                                >
                                    <Sparkles className="h-4 w-4" />
                                    IA — Insights
                                </Link>
                            ) : null}
                        </div>
                    </Card>
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <div className="flex items-center justify-between gap-2">
                            <h2 className="font-bold text-navy-900">Estudiantes con nivel básico o sin diagnóstico</h2>
                            <TriangleAlert className="h-5 w-5 text-amber-600" aria-hidden />
                        </div>
                        <ul className="mt-4 divide-y divide-slate-100">
                            {low_students.length === 0 ? (
                                <li className="py-2 text-sm text-plomo">Sin registros en sus secciones.</li>
                            ) : (
                                low_students.slice(0, 15).map((s) => (
                                    <li key={s.student_id} className="py-2 text-sm">
                                        <span className="font-medium">{s.full_name}</span>
                                        <span className="ml-2 text-plomo">
                                            ({s.code}) — {s.level ?? 'sin nivel'} — {s.score ?? '—'}%
                                        </span>
                                    </li>
                                ))
                            )}
                        </ul>
                    </Card>
                    <Card>
                        <div className="flex items-center justify-between gap-2">
                            <h2 className="font-bold text-navy-900">Temas débiles</h2>
                            <ClipboardList className="h-5 w-5 text-slate-500" aria-hidden />
                        </div>
                        <ul className="mt-4 space-y-2">
                            {topicEntries.length === 0 ? (
                                <li className="text-sm text-plomo">Sin datos aún en intentos de sus estudiantes.</li>
                            ) : (
                                topicEntries.map(([t, n]) => (
                                    <li key={t} className="flex justify-between text-sm">
                                        <span>{t}</span>
                                        <span className="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-navy-900">
                                            {n}
                                        </span>
                                    </li>
                                ))
                            )}
                        </ul>
                    </Card>
                </div>
            </PageContainer>
        </TeacherLayout>
    );
}
