import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';

type Opt = { id: number; name: string; year?: number; grade_id?: number; educational_level_id?: number };
type ModeOpt = { value: string; label: string };

type Props = PageProps<{
    assignments: { section_id: number; academic_year_id: number; section?: string; subject_id?: number | null }[];
    subjects: Opt[];
    sections: (Opt & { grade?: { name: string } })[];
    grades: (Opt & { educational_level_id?: number })[];
    educational_levels: Opt[];
    academic_years: Opt[];
    modes: ModeOpt[];
}>;

export default function TeacherDiagnosticsCreate() {
    const { subjects, sections, grades, educational_levels, academic_years, modes } = usePage<Props>().props;

    const form = useForm({
        title: '',
        description: '',
        subject_id: '' as string | number,
        academic_year_id: '' as string | number,
        educational_level_id: '' as string | number,
        grade_id: '' as string | number,
        section_id: '' as string | number,
        mode: modes[0]?.value ?? 'fixed',
        is_active: true,
        prevent_retake_after_completion: false,
        adaptive_question_count: 8,
        threshold_basic_percent: 40,
        threshold_intermediate_percent: 70,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.transform((data) => ({
            ...data,
            subject_id: data.subject_id === '' ? null : Number(data.subject_id),
            academic_year_id: Number(data.academic_year_id),
            educational_level_id: data.educational_level_id === '' ? null : Number(data.educational_level_id),
            grade_id: data.grade_id === '' ? null : Number(data.grade_id),
            section_id: Number(data.section_id),
        }));
        form.post(route('teacher.diagnostics.store'));
    };

    return (
        <TeacherLayout title="Nuevo diagnóstico">
            <Head title="Nuevo diagnóstico" />
            <PageContainer>
                <SectionTitle
                    title="Crear diagnóstico"
                    description="El alcance debe coincidir con una sección donde tenga asignación activa (tutor o curso). Las preguntas fijas se enlazan después desde administración o flujos de banco según políticas."
                />

                <Card>
                    <form onSubmit={submit} className="mx-auto max-w-2xl space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-navy-900">Título</label>
                            <input
                                className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                value={form.data.title}
                                onChange={(e) => form.setData('title', e.target.value)}
                                required
                            />
                            {form.errors.title ? <p className="mt-1 text-xs text-red-600">{form.errors.title}</p> : null}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-navy-900">Descripción (opcional)</label>
                            <textarea
                                className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                rows={3}
                                value={form.data.description}
                                onChange={(e) => form.setData('description', e.target.value)}
                            />
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium text-navy-900">Año académico</label>
                                <select
                                    className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                    value={form.data.academic_year_id}
                                    onChange={(e) => form.setData('academic_year_id', e.target.value)}
                                    required
                                >
                                    <option value="">Seleccione…</option>
                                    {academic_years.map((y) => (
                                        <option key={y.id} value={y.id}>
                                            {y.name}
                                        </option>
                                    ))}
                                </select>
                                {form.errors.academic_year_id ? (
                                    <p className="mt-1 text-xs text-red-600">{form.errors.academic_year_id}</p>
                                ) : null}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-navy-900">Sección</label>
                                <select
                                    className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                    value={form.data.section_id}
                                    onChange={(e) => form.setData('section_id', e.target.value)}
                                    required
                                >
                                    <option value="">Seleccione…</option>
                                    {sections.map((s) => (
                                        <option key={s.id} value={s.id}>
                                            {s.name}
                                            {s.grade ? ` — ${s.grade.name}` : ''}
                                        </option>
                                    ))}
                                </select>
                                {form.errors.section_id ? (
                                    <p className="mt-1 text-xs text-red-600">{form.errors.section_id}</p>
                                ) : null}
                            </div>
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium text-navy-900">Nivel educativo (opcional)</label>
                                <select
                                    className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                    value={form.data.educational_level_id}
                                    onChange={(e) => form.setData('educational_level_id', e.target.value)}
                                >
                                    <option value="">—</option>
                                    {educational_levels.map((l) => (
                                        <option key={l.id} value={l.id}>
                                            {l.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-navy-900">Grado (opcional)</label>
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
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-navy-900">Curso / asignatura (opcional)</label>
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
                                <label className="block text-sm font-medium text-navy-900">Modo</label>
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
                                <label className="block text-sm font-medium text-navy-900">Cantidad preguntas (adaptativo)</label>
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
                                <label className="block text-sm font-medium text-navy-900">Umbral básico (%)</label>
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
                                <label className="block text-sm font-medium text-navy-900">Umbral intermedio (%)</label>
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
                                Activo para estudiantes
                            </label>
                            <label className="flex items-center gap-2 text-sm">
                                <input
                                    type="checkbox"
                                    checked={form.data.prevent_retake_after_completion}
                                    onChange={(e) => form.setData('prevent_retake_after_completion', e.target.checked)}
                                />
                                Evitar reintento tras completar
                            </label>
                        </div>
                        <div className="flex gap-3 pt-2">
                            <button
                                type="submit"
                                disabled={form.processing}
                                className="rounded-lg bg-brand-navy px-4 py-2 text-sm font-semibold text-white hover:bg-navy-800 disabled:opacity-50"
                            >
                                Guardar
                            </button>
                            <Link href={route('teacher.diagnostics.index')} className="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-navy-900 hover:bg-slate-50">
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </Card>
            </PageContainer>
        </TeacherLayout>
    );
}
