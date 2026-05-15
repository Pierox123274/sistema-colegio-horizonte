import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import PrimaryButton from '@/Components/PrimaryButton';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler, useMemo, useState } from 'react';

type Level = {
    id: number;
    name: string;
    grades: Array<{
        id: number;
        name: string;
        sections: Array<{ id: number; name: string }>;
    }>;
};

type Catalog = {
    teachers: SelectOption[];
    academic_years: Array<{ value: string; label: string }>;
    levels: Level[];
    subjects: SelectOption[];
};

type Props = PageProps<{ catalog: Catalog }>;

export default function TeacherAssignmentsCreate() {
    const { catalog } = usePage<Props>().props;

    const activeYear =
        catalog.academic_years.find((y) => y.label.includes('Activo'))?.value ??
        catalog.academic_years[0]?.value ??
        '';

    const [levelId, setLevelId] = useState('');
    const [gradeId, setGradeId] = useState('');
    const [sectionId, setSectionId] = useState('');

    const form = useForm({
        user_id: '',
        academic_year_id: activeYear,
        educational_level_id: '',
        grade_id: '',
        section_id: '',
        subject_id: '',
        is_tutor: false,
        is_active: true,
    });

    const grades = useMemo(
        () => catalog.levels.find((l) => String(l.id) === levelId)?.grades ?? [],
        [catalog.levels, levelId],
    );
    const sections = useMemo(
        () => grades.find((g) => String(g.id) === gradeId)?.sections ?? [],
        [grades, gradeId],
    );

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.post(route('intranet.admin.teacher-assignments.store'));
    };

    return (
        <IntranetLayout title="Nueva asignación">
            <Head title="Administración — Nueva asignación docente" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Administración' },
                        {
                            label: 'Asignaciones',
                            href: route('intranet.admin.teacher-assignments.index'),
                        },
                        { label: 'Nueva' },
                    ]}
                />
                <SectionTitle
                    title="Nueva asignación docente"
                    description="Solo usuarios con rol Docente pueden asignarse."
                    actions={
                        <Link
                            href={route('intranet.admin.teacher-assignments.index')}
                            className="text-sm font-semibold text-navy-900 underline"
                        >
                            Volver
                        </Link>
                    }
                />

                <Card>
                    <form onSubmit={submit} className="grid max-w-2xl gap-4">
                        <div>
                            <label className="text-xs font-semibold uppercase text-plomo">
                                Docente
                            </label>
                            <select
                                value={form.data.user_id}
                                onChange={(e) => form.setData('user_id', e.target.value)}
                                className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">Seleccionar</option>
                                {catalog.teachers.map((t) => (
                                    <option key={t.value} value={t.value}>
                                        {t.label}
                                    </option>
                                ))}
                            </select>
                            {form.errors.user_id ? (
                                <p className="mt-1 text-xs text-red-600">{form.errors.user_id}</p>
                            ) : null}
                        </div>

                        <div>
                            <label className="text-xs font-semibold uppercase text-plomo">
                                Año académico
                            </label>
                            <select
                                value={form.data.academic_year_id}
                                onChange={(e) => form.setData('academic_year_id', e.target.value)}
                                className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                {catalog.academic_years.map((y) => (
                                    <option key={y.value} value={y.value}>
                                        {y.label}
                                    </option>
                                ))}
                            </select>
                            {form.errors.academic_year_id ? (
                                <p className="mt-1 text-xs text-red-600">
                                    {form.errors.academic_year_id}
                                </p>
                            ) : null}
                        </div>

                        <div>
                            <label className="text-xs font-semibold uppercase text-plomo">
                                Nivel educativo
                            </label>
                            <select
                                value={levelId}
                                onChange={(e) => {
                                    const v = e.target.value;
                                    setLevelId(v);
                                    setGradeId('');
                                    setSectionId('');
                                    form.setData('educational_level_id', v);
                                    form.setData('grade_id', '');
                                    form.setData('section_id', '');
                                }}
                                className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">Seleccionar</option>
                                {catalog.levels.map((l) => (
                                    <option key={l.id} value={l.id}>
                                        {l.name}
                                    </option>
                                ))}
                            </select>
                            {form.errors.educational_level_id ? (
                                <p className="mt-1 text-xs text-red-600">
                                    {form.errors.educational_level_id}
                                </p>
                            ) : null}
                        </div>

                        <div>
                            <label className="text-xs font-semibold uppercase text-plomo">
                                Grado
                            </label>
                            <select
                                value={gradeId}
                                onChange={(e) => {
                                    const v = e.target.value;
                                    setGradeId(v);
                                    setSectionId('');
                                    form.setData('grade_id', v);
                                    form.setData('section_id', '');
                                }}
                                className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">Seleccionar</option>
                                {grades.map((g) => (
                                    <option key={g.id} value={g.id}>
                                        {g.name}
                                    </option>
                                ))}
                            </select>
                            {form.errors.grade_id ? (
                                <p className="mt-1 text-xs text-red-600">{form.errors.grade_id}</p>
                            ) : null}
                        </div>

                        <div>
                            <label className="text-xs font-semibold uppercase text-plomo">
                                Sección
                            </label>
                            <select
                                value={sectionId}
                                onChange={(e) => {
                                    const v = e.target.value;
                                    setSectionId(v);
                                    form.setData('section_id', v);
                                }}
                                className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">Seleccionar</option>
                                {sections.map((s) => (
                                    <option key={s.id} value={s.id}>
                                        {s.name}
                                    </option>
                                ))}
                            </select>
                            {form.errors.section_id ? (
                                <p className="mt-1 text-xs text-red-600">{form.errors.section_id}</p>
                            ) : null}
                        </div>

                        <div>
                            <label className="text-xs font-semibold uppercase text-plomo">
                                Curso (opcional)
                            </label>
                            <select
                                value={form.data.subject_id}
                                onChange={(e) => form.setData('subject_id', e.target.value)}
                                className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                <option value="">— Sin curso específico —</option>
                                {catalog.subjects.map((s) => (
                                    <option key={s.value} value={s.value}>
                                        {s.label}
                                    </option>
                                ))}
                            </select>
                            {form.errors.subject_id ? (
                                <p className="mt-1 text-xs text-red-600">{form.errors.subject_id}</p>
                            ) : null}
                        </div>

                        <label className="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                checked={form.data.is_tutor}
                                onChange={(e) => form.setData('is_tutor', e.target.checked)}
                            />
                            Tutor de aula (encargado)
                        </label>

                        <label className="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                checked={form.data.is_active}
                                onChange={(e) => form.setData('is_active', e.target.checked)}
                            />
                            Asignación activa
                        </label>

                        <div className="flex gap-3 pt-2">
                            <PrimaryButton disabled={form.processing}>Guardar</PrimaryButton>
                            <Link
                                href={route('intranet.admin.teacher-assignments.index')}
                                className="py-2 text-sm text-plomo hover:text-navy-900"
                            >
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
