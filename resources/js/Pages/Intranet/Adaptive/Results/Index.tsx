import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type Row = {
    id: number;
    student_name: string | null;
    student_code: string | null;
    exam_title: string | null;
    score_percent: number | null;
    classified_level: string | null;
    completed_at: string | null;
};

type Props = PageProps<{
    attempts: { data: Row[] };
}>;

export default function IntranetAdaptiveResultsIndex() {
    const { attempts } = usePage<Props>().props;

    return (
        <IntranetLayout title="Resultados diagnóstico">
            <Head title="Resultados diagnóstico" />
            <PageContainer>
                <SectionTitle
                    title="Resultados de diagnósticos"
                    description="Intentos completados a nivel institución (lectura para secretaría y administración)."
                />
                <Card>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-left text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 text-xs uppercase text-plomo">
                                    <th className="py-3 pr-3 font-semibold">Estudiante</th>
                                    <th className="py-3 pr-3 font-semibold">Código</th>
                                    <th className="py-3 pr-3 font-semibold">Examen</th>
                                    <th className="py-3 pr-3 font-semibold">Puntaje</th>
                                    <th className="py-3 pr-3 font-semibold">Nivel</th>
                                    <th className="py-3 font-semibold">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                {attempts.data.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="py-8 text-center text-plomo">
                                            Sin intentos completados.
                                        </td>
                                    </tr>
                                ) : (
                                    attempts.data.map((a) => (
                                        <tr key={a.id} className="border-b border-slate-100">
                                            <td className="py-3 pr-3">{a.student_name ?? '—'}</td>
                                            <td className="py-3 pr-3 text-plomo">{a.student_code ?? '—'}</td>
                                            <td className="py-3 pr-3">{a.exam_title ?? '—'}</td>
                                            <td className="py-3 pr-3">{a.score_percent ?? '—'}%</td>
                                            <td className="py-3 pr-3 capitalize text-plomo">{a.classified_level ?? '—'}</td>
                                            <td className="py-3 text-plomo">
                                                {a.completed_at ? new Date(a.completed_at).toLocaleString() : '—'}
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
