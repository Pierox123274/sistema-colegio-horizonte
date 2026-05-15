import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';

type EvaluationRow = {
    id: number;
    title: string;
    period: string;
    evaluated_at: string;
    subject: { name: string; code: string } | null;
    section: { name: string } | null;
};

type Props = PageProps<{
    evaluations: { data: EvaluationRow[] };
    filters: { search: string; subject_id: string; section_id: string; academic_year_id: string };
    catalog: { subjects: SelectOption[]; academic_years: SelectOption[] };
}>;

export default function EvaluationIndex() {
    const { evaluations, filters, catalog } = usePage<Props>().props;

    return (
        <IntranetLayout title="Evaluaciones">
            <Head title="Evaluaciones" />
            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Gestión académica' }, { label: 'Evaluaciones' }]} />
                <SectionTitle
                    title="Evaluaciones académicas"
                    description="Administra evaluaciones por curso y sección."
                    actions={<Link href={route('intranet.academic.evaluations.create')} className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white">Nueva evaluación</Link>}
                />
                <Card>
                    <div className="mb-4 grid gap-3 md:grid-cols-3">
                        <input defaultValue={filters.search} onBlur={(e) => router.get(route('intranet.academic.evaluations.index'), { ...filters, search: e.target.value }, { preserveState: true })} className="rounded-md border border-plomo/20 px-3 py-2 text-sm" placeholder="Buscar" />
                        <select defaultValue={filters.subject_id} onChange={(e) => router.get(route('intranet.academic.evaluations.index'), { ...filters, subject_id: e.target.value }, { preserveState: true })} className="rounded-md border border-plomo/20 px-3 py-2 text-sm">
                            <option value="">Todos los cursos</option>
                            {catalog.subjects.map((subject) => <option key={subject.value} value={subject.value}>{subject.label}</option>)}
                        </select>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b border-plomo/15 text-left">
                                    <th className="px-3 py-2">Evaluación</th>
                                    <th className="px-3 py-2">Curso</th>
                                    <th className="px-3 py-2">Sección</th>
                                    <th className="px-3 py-2">Fecha</th>
                                    <th className="px-3 py-2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {evaluations.data.map((evaluation) => (
                                    <tr key={evaluation.id} className="border-b border-plomo/10">
                                        <td className="px-3 py-2"><p className="font-semibold text-navy-900">{evaluation.title}</p><p className="text-xs text-plomo">{evaluation.period}</p></td>
                                        <td className="px-3 py-2">{evaluation.subject?.name ?? '-'}</td>
                                        <td className="px-3 py-2">{evaluation.section?.name ?? '-'}</td>
                                        <td className="px-3 py-2">{evaluation.evaluated_at}</td>
                                        <td className="px-3 py-2">
                                            <div className="flex gap-2">
                                                <Link href={route('intranet.academic.evaluations.show', evaluation.id)} className="rounded border border-plomo/20 px-2 py-1 text-xs font-semibold">Ver</Link>
                                                <Link href={route('intranet.academic.evaluations.edit', evaluation.id)} className="rounded border border-plomo/20 px-2 py-1 text-xs font-semibold">Editar</Link>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}

