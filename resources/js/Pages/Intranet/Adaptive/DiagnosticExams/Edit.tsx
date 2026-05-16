import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';

type Opt = { id: number; name: string; year?: number; grade_id?: number; educational_level_id?: number };
type ModeOpt = { value: string; label: string };

type ExamForm = {
    id: number;
    title: string;
    description: string | null;
    subject_id: number | null;
    academic_year_id: number | null;
    educational_level_id: number | null;
    grade_id: number | null;
    section_id: number | null;
    mode: string;
    is_active: boolean;
    prevent_retake_after_completion: boolean;
    adaptive_question_count: number;
    threshold_basic_percent: number;
    threshold_intermediate_percent: number;
};

type Props = PageProps<{
    exam: ExamForm;
    academic_years: Opt[];
    levels: Opt[];
    grades: Opt[];
    sections: Opt[];
    subjects: Opt[];
    modes: ModeOpt[];
}>;

export default function IntranetDiagnosticExamsEdit() {
    const { exam, academic_years, levels, grades, sections, subjects, modes } = usePage<Props>().props;

    const form = useForm({
        title: exam.title,
        description: exam.description ?? '',
        subject_id: exam.subject_id ?? ('' as string | number),
        academic_year_id: exam.academic_year_id ?? ('' as string | number),
        educational_level_id: exam.educational_level_id ?? ('' as string | number),
        grade_id: exam.grade_id ?? ('' as string | number),
        section_id: exam.section_id ?? ('' as string | number),
        mode: exam.mode,
        is_active: exam.is_active,
        prevent_retake_after_completion: exam.prevent_retake_after_completion,
        adaptive_question_count: exam.adaptive_question_count,
        threshold_basic_percent: exam.threshold_basic_percent,
        threshold_intermediate_percent: exam.threshold_intermediate_percent,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.transform((data) => ({
            ...data,
            subject_id: data.subject_id === '' ? null : Number(data.subject_id),
            academic_year_id: data.academic_year_id === '' ? null : Number(data.academic_year_id),
            educational_level_id: data.educational_level_id === '' ? null : Number(data.educational_level_id),
            grade_id: data.grade_id === '' ? null : Number(data.grade_id),
            section_id: data.section_id === '' ? null : Number(data.section_id),
        }));
        form.put(route('intranet.adaptive.diagnostic-exams.update', exam.id));
    };

    return (
        <IntranetLayout title="Editar diagnóstico">
            <Head title="Editar diagnóstico" />
            <PageContainer>
                <SectionTitle title="Editar examen diagnóstico" description={exam.title} />
                <Card>
                    <form onSubmit={submit} className="mx-auto max-w-2xl space-y-4">
                        <div>
                            <label className="block text-sm font-medium">Título</label>
                            <input
                                className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                value={form.data.title}
                                onChange={(e) => form.setData('title', e.target.value)}
                                required
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium">Descripción</label>
                            <textarea
                                className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                rows={2}
                                value={form.data.description}
                                onChange={(e) => form.setData('description', e.target.value)}
                            />
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium">Año académico</label>
                                <select
                                    className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                    value={form.data.academic_year_id}
                                    onChange={(e) => form.setData('academic_year_id', e.target.value)}
                                >
                                    <option value="">—</option>
                                    {academic_years.map((y) => (
                                        <option key={y.id} value={y.id}>
                                            {y.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium">Nivel</label>
                                <select
                                    className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                    value={form.data.educational_level_id}
                                    onChange={(e) => form.setData('educational_level_id', e.target.value)}
                                >
                                    <option value="">—</option>
                                    {levels.map((l) => (
                                        <option key={l.id} value={l.id}>
                                            {l.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium">Grado</label>
                                <select
                                    className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                    value={form.data.grade_id}
                                    onChange={(e) => form.setData('grade_id', e.target.value)}
                                >
                                    <option value="">—</option>
                                    {grades.map((g) => (
                                        <option key={g.id} value={g.id}>
                                            {g.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium">Sección</label>
                                <select
                                    className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                    value={form.data.section_id}
                                    onChange={(e) => form.setData('section_id', e.target.value)}
                                >
                                    <option value="">—</option>
                                    {sections.map((s) => (
                                        <option key={s.id} value={s.id}>
                                            {s.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>
                        <div>
                            <label className="block text-sm font-medium">Curso</label>
                            <select
                                className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                value={form.data.subject_id}
                                onChange={(e) => form.setData('subject_id', e.target.value)}
                            >
                                <option value="">—</option>
                                {subjects.map((s) => (
                                    <option key={s.id} value={s.id}>
                                        {s.name}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium">Modo</label>
                                <select
                                    className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                    value={form.data.mode}
                                    onChange={(e) => form.setData('mode', e.target.value)}
                                >
                                    {modes.map((m) => (
                                        <option key={m.value} value={m.value}>
                                            {m.label}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium">Preguntas adaptativo</label>
                                <input
                                    type="number"
                                    min={1}
                                    max={100}
                                    className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                    value={form.data.adaptive_question_count}
                                    onChange={(e) => form.setData('adaptive_question_count', Number(e.target.value))}
                                />
                            </div>
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium">Umbral básico %</label>
                                <input
                                    type="number"
                                    min={0}
                                    max={98}
                                    className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                    value={form.data.threshold_basic_percent}
                                    onChange={(e) => form.setData('threshold_basic_percent', Number(e.target.value))}
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium">Umbral intermedio %</label>
                                <input
                                    type="number"
                                    min={1}
                                    max={99}
                                    className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                    value={form.data.threshold_intermediate_percent}
                                    onChange={(e) => form.setData('threshold_intermediate_percent', Number(e.target.value))}
                                />
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-4">
                            <label className="flex items-center gap-2 text-sm">
                                <input
                                    type="checkbox"
                                    checked={form.data.is_active}
                                    onChange={(e) => form.setData('is_active', e.target.checked)}
                                />
                                Activo
                            </label>
                            <label className="flex items-center gap-2 text-sm">
                                <input
                                    type="checkbox"
                                    checked={form.data.prevent_retake_after_completion}
                                    onChange={(e) => form.setData('prevent_retake_after_completion', e.target.checked)}
                                />
                                Sin reintento tras completar
                            </label>
                        </div>
                        <div className="flex gap-3">
                            <button
                                type="submit"
                                disabled={form.processing}
                                className="rounded-lg bg-brand-navy px-4 py-2 text-sm font-semibold text-white disabled:opacity-50"
                            >
                                Actualizar
                            </button>
                            <Link href={route('intranet.adaptive.diagnostic-exams.index')} className="rounded-lg border px-4 py-2 text-sm">
                                Volver
                            </Link>
                        </div>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
