import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';

type Catalog = {
    subjects: SelectOption[];
    academic_years: SelectOption[];
    levels: Array<{ id: number; name: string; grades: Array<{ id: number; name: string; sections: Array<{ id: number; name: string }> }> }>;
};

type Props = PageProps<{ catalog: Catalog }>;

export default function EvaluationCreate() {
    const { catalog } = usePage<Props>().props;
    const [levelId, setLevelId] = useState('');
    const [gradeId, setGradeId] = useState('');
    const grades = useMemo(() => catalog.levels.find((level) => String(level.id) === levelId)?.grades ?? [], [catalog.levels, levelId]);
    const sections = useMemo(() => grades.find((grade) => String(grade.id) === gradeId)?.sections ?? [], [grades, gradeId]);
    const form = useForm({
        subject_id: '',
        academic_year_id: catalog.academic_years[0]?.value ?? '',
        educational_level_id: '',
        grade_id: '',
        section_id: '',
        title: '',
        period: 'Bimestre 1',
        evaluated_at: new Date().toISOString().slice(0, 10),
        max_score: '20',
        weight: '1',
        is_active: true,
    });

    return (
        <IntranetLayout title="Nueva evaluación">
            <Head title="Nueva evaluación" />
            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Gestión académica' }, { label: 'Evaluaciones' }, { label: 'Nueva' }]} />
                <SectionTitle title="Crear evaluación" description="Define evaluación por curso, sección y periodo." actions={<Link href={route('intranet.academic.evaluations.index')} className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold">Volver</Link>} />
                <Card>
                    <form className="grid gap-4 md:grid-cols-2" onSubmit={(e) => {
                        e.preventDefault();
                        form.post(route('intranet.academic.evaluations.store'));
                    }}>
                        <div><label className="text-xs font-semibold uppercase text-plomo">Curso</label><select value={form.data.subject_id} onChange={(e) => form.setData('subject_id', e.target.value)} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Seleccionar</option>{catalog.subjects.map((subject) => <option key={subject.value} value={subject.value}>{subject.label}</option>)}</select></div>
                        <div><label className="text-xs font-semibold uppercase text-plomo">Año académico</label><select value={form.data.academic_year_id} onChange={(e) => form.setData('academic_year_id', e.target.value)} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm">{catalog.academic_years.map((year) => <option key={year.value} value={year.value}>{year.label}</option>)}</select></div>
                        <div><label className="text-xs font-semibold uppercase text-plomo">Nivel</label><select value={levelId} onChange={(e) => { setLevelId(e.target.value); setGradeId(''); form.setData('educational_level_id', e.target.value); form.setData('grade_id', ''); form.setData('section_id', ''); }} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Seleccionar</option>{catalog.levels.map((level) => <option key={level.id} value={level.id}>{level.name}</option>)}</select></div>
                        <div><label className="text-xs font-semibold uppercase text-plomo">Grado</label><select value={gradeId} onChange={(e) => { setGradeId(e.target.value); form.setData('grade_id', e.target.value); form.setData('section_id', ''); }} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Seleccionar</option>{grades.map((grade) => <option key={grade.id} value={grade.id}>{grade.name}</option>)}</select></div>
                        <div><label className="text-xs font-semibold uppercase text-plomo">Sección</label><select value={form.data.section_id} onChange={(e) => form.setData('section_id', e.target.value)} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Seleccionar</option>{sections.map((section) => <option key={section.id} value={section.id}>{section.name}</option>)}</select></div>
                        <div><label className="text-xs font-semibold uppercase text-plomo">Fecha</label><input type="date" value={form.data.evaluated_at} onChange={(e) => form.setData('evaluated_at', e.target.value)} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm" /></div>
                        <div><label className="text-xs font-semibold uppercase text-plomo">Título</label><input value={form.data.title} onChange={(e) => form.setData('title', e.target.value)} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm" /></div>
                        <div><label className="text-xs font-semibold uppercase text-plomo">Periodo</label><input value={form.data.period} onChange={(e) => form.setData('period', e.target.value)} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm" /></div>
                        <div><label className="text-xs font-semibold uppercase text-plomo">Nota máxima</label><input type="number" step="0.01" min={1} max={20} value={form.data.max_score} onChange={(e) => form.setData('max_score', e.target.value)} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm" /></div>
                        <div><label className="text-xs font-semibold uppercase text-plomo">Peso</label><input type="number" step="0.01" min={0.1} value={form.data.weight} onChange={(e) => form.setData('weight', e.target.value)} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm" /></div>
                        <div className="md:col-span-2"><button type="submit" disabled={form.processing} className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Guardar evaluación</button></div>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}

