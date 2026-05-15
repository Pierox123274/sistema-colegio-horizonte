import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { useEffect } from 'react';

type Batch = {
    students: Array<{ id: number; code: string; first_name: string; last_name: string; document_number: string | null }>;
    records: Record<string, { score: number; observations: string | null }>;
} | null;

type Props = PageProps<{
    filters: { academic_year_id: string; section_id: string; subject_id: string; evaluation_id: string; student_id: string };
    catalog: { academic_years: SelectOption[]; sections: SelectOption[]; subjects: SelectOption[]; evaluations: SelectOption[]; students: SelectOption[] };
    batch: Batch;
    selected_evaluation: { id: number; label: string } | null;
}>;

export default function GradeRecordsIndex() {
    const { filters, catalog, batch, selected_evaluation } = usePage<Props>().props;
    const form = useForm({
        evaluation_id: filters.evaluation_id,
        entries: batch?.students.map((student) => ({
            student_id: String(student.id),
            score: String(batch.records[String(student.id)]?.score ?? ''),
            observations: batch.records[String(student.id)]?.observations ?? '',
        })) ?? [],
    });

    useEffect(() => {
        form.setData('evaluation_id', filters.evaluation_id);
        form.setData('entries', batch?.students.map((student) => ({
            student_id: String(student.id),
            score: String(batch.records[String(student.id)]?.score ?? ''),
            observations: batch.records[String(student.id)]?.observations ?? '',
        })) ?? []);
    }, [filters.evaluation_id, batch]);

    return (
        <IntranetLayout title="Registro de notas">
            <Head title="Registro de notas" />
            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Gestión académica' }, { label: 'Registro de notas' }]} />
                <SectionTitle
                    title="Registro de notas"
                    description="Carga notas por evaluación para estudiantes matriculados."
                    actions={<Link href={route('intranet.academic.grades.reports.index')} className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold">Ver reportes</Link>}
                />
                <Card className="mb-6">
                    <div className="grid gap-3 md:grid-cols-5">
                        <select defaultValue={filters.academic_year_id} onChange={(e) => router.get(route('intranet.academic.grades.records.index'), { ...filters, academic_year_id: e.target.value }, { preserveState: true })} className="rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Año académico</option>{catalog.academic_years.map((item) => <option key={item.value} value={item.value}>{item.label}</option>)}</select>
                        <select defaultValue={filters.section_id} onChange={(e) => router.get(route('intranet.academic.grades.records.index'), { ...filters, section_id: e.target.value }, { preserveState: true })} className="rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Sección</option>{catalog.sections.map((item) => <option key={item.value} value={item.value}>{item.label}</option>)}</select>
                        <select defaultValue={filters.subject_id} onChange={(e) => router.get(route('intranet.academic.grades.records.index'), { ...filters, subject_id: e.target.value }, { preserveState: true })} className="rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Curso</option>{catalog.subjects.map((item) => <option key={item.value} value={item.value}>{item.label}</option>)}</select>
                        <select defaultValue={filters.evaluation_id} onChange={(e) => router.get(route('intranet.academic.grades.records.index'), { ...filters, evaluation_id: e.target.value }, { preserveState: true })} className="rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Evaluación</option>{catalog.evaluations.map((item) => <option key={item.value} value={item.value}>{item.label}</option>)}</select>
                        <div className="flex gap-2">
                            <a href={`${route('intranet.academic.grades.reports.export.pdf')}?${new URLSearchParams(filters).toString()}`} className="rounded-md border border-plomo/20 px-3 py-2 text-sm font-semibold">PDF</a>
                            <a href={`${route('intranet.academic.grades.reports.export.excel')}?${new URLSearchParams(filters).toString()}`} className="rounded-md border border-plomo/20 px-3 py-2 text-sm font-semibold">CSV</a>
                        </div>
                    </div>
                </Card>

                {batch && selected_evaluation ? (
                    <Card>
                        <p className="mb-3 text-sm font-semibold text-navy-900">{selected_evaluation.label}</p>
                        {batch.students.length === 0 ? (
                            <p className="text-sm text-amber-700">No hay estudiantes matriculados para esta evaluación.</p>
                        ) : (
                            <form onSubmit={(e) => {
                                e.preventDefault();
                                form.post(route('intranet.academic.grades.records.store'));
                            }}>
                                <div className="space-y-3">
                                    {batch.students.map((student, idx) => (
                                        <div key={student.id} className="grid gap-3 rounded-lg border border-plomo/10 p-3 md:grid-cols-[2fr_1fr_2fr_auto]">
                                            <div>
                                                <p className="font-semibold text-navy-900">{student.last_name}, {student.first_name}</p>
                                                <p className="text-xs text-plomo">{student.code} · {student.document_number ?? 'Sin documento'}</p>
                                            </div>
                                            <div>
                                                <input type="number" min={0} max={20} step="0.01" value={form.data.entries[idx]?.score ?? ''} onChange={(e) => form.setData('entries', form.data.entries.map((entry, i) => i === idx ? { ...entry, score: e.target.value } : entry))} className="w-full rounded-md border border-plomo/20 px-3 py-2 text-sm" placeholder="Nota" />
                                            </div>
                                            <div>
                                                <input value={form.data.entries[idx]?.observations ?? ''} onChange={(e) => form.setData('entries', form.data.entries.map((entry, i) => i === idx ? { ...entry, observations: e.target.value } : entry))} className="w-full rounded-md border border-plomo/20 px-3 py-2 text-sm" placeholder="Observaciones" />
                                            </div>
                                            <div className="flex items-center">
                                                <Link href={route('intranet.academic.grades.students.show', student.id)} className="rounded border border-plomo/20 px-2 py-1 text-xs font-semibold">Historial</Link>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                                <div className="mt-4 flex justify-end">
                                    <button type="submit" disabled={form.processing} className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Guardar notas</button>
                                </div>
                            </form>
                        )}
                    </Card>
                ) : (
                    <Card><p className="text-sm text-plomo">Selecciona una evaluación para registrar notas.</p></Card>
                )}
            </PageContainer>
        </IntranetLayout>
    );
}

