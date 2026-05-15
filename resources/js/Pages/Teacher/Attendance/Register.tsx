import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { CheckCircle2, Clock3, FileCheck, Search, XCircle } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';

type Catalog = {
    statuses: SelectOption[];
    academic_years: Array<{ value: string; label: string; is_active: boolean }>;
    levels: Array<{
        id: number;
        name: string;
        grades: Array<{ id: number; name: string; sections: Array<{ id: number; name: string }> }>;
    }>;
};

type Batch = {
    students: Array<{ id: number; code: string; first_name: string; last_name: string; document_number: string | null }>;
    records: Record<string, { status: string; observation: string | null }>;
    context: { attendance_date: string; section_id: number; grade_id: number; educational_level_id: number; academic_year_id: number | null };
} | null;

type Entry = { student_id: string; status: string; observation: string };

type P = PageProps<{
    catalog: Catalog;
    batch: Batch;
    initial?: { section_id: string };
}>;

export default function TeacherAttendanceRegister() {
    const { catalog, batch, initial } = usePage<P>().props;
    const activeYear = catalog.academic_years.find((y) => y.is_active)?.value ?? catalog.academic_years[0]?.value ?? '';

    const [academicYearId, setAcademicYearId] = useState(batch?.context.academic_year_id ? String(batch.context.academic_year_id) : activeYear);
    const [date, setDate] = useState(batch?.context.attendance_date ?? new Date().toISOString().slice(0, 10));
    const [levelId, setLevelId] = useState(batch?.context.educational_level_id ? String(batch.context.educational_level_id) : '');
    const [gradeId, setGradeId] = useState(batch?.context.grade_id ? String(batch.context.grade_id) : '');
    const [sectionId, setSectionId] = useState(batch?.context.section_id ? String(batch.context.section_id) : '');

    const grades = useMemo(() => catalog.levels.find((l) => String(l.id) === levelId)?.grades ?? [], [catalog.levels, levelId]);
    const sections = useMemo(() => grades.find((g) => String(g.id) === gradeId)?.sections ?? [], [grades, gradeId]);

    useEffect(() => {
        if (batch?.context || !initial?.section_id) {
            return;
        }
        const targetSectionId = initial.section_id;
        for (const level of catalog.levels) {
            for (const grade of level.grades) {
                const match = grade.sections.find((s) => String(s.id) === targetSectionId);
                if (match) {
                    setLevelId(String(level.id));
                    setGradeId(String(grade.id));
                    setSectionId(targetSectionId);
                    return;
                }
            }
        }
    }, [batch?.context, catalog.levels, initial?.section_id]);

    const form = useForm<{
        academic_year_id: string;
        educational_level_id: string;
        grade_id: string;
        section_id: string;
        attendance_date: string;
        entries: Entry[];
    }>({
        academic_year_id: academicYearId,
        educational_level_id: levelId,
        grade_id: gradeId,
        section_id: sectionId,
        attendance_date: date,
        entries: batch?.students.map((s) => ({
            student_id: String(s.id),
            status: batch.records[String(s.id)]?.status ?? 'presente',
            observation: batch.records[String(s.id)]?.observation ?? '',
        })) ?? [],
    });

    const loadStudents = () => {
        if (!date || !sectionId) return;
        router.get(route('teacher.attendance.section-date', { date, section: sectionId }), { academic_year_id: academicYearId || undefined }, { preserveState: false });
    };

    const statusClass = (status: string) => {
        if (status === 'presente') return 'bg-emerald-100 text-emerald-800';
        if (status === 'tarde') return 'bg-amber-100 text-amber-800';
        if (status === 'falta') return 'bg-red-100 text-red-800';
        return 'bg-blue-100 text-blue-800';
    };

    const statusIcon = (status: string) => {
        if (status === 'presente') return <CheckCircle2 className="h-4 w-4" />;
        if (status === 'tarde') return <Clock3 className="h-4 w-4" />;
        if (status === 'falta') return <XCircle className="h-4 w-4" />;
        return <FileCheck className="h-4 w-4" />;
    };

    return (
        <TeacherLayout title="Registrar asistencia">
            <Head title="Portal docente — Registrar asistencia" />
            <PageContainer>
                <SectionTitle
                    title="Registro de asistencia"
                    description="Seleccione fecha y una de sus secciones asignadas; luego marque el estado por estudiante matriculado."
                    actions={<Link href={route('teacher.attendance.index')} className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900">Volver a asistencia</Link>}
                />

                <Card className="mb-6">
                    <div className="grid gap-4 md:grid-cols-6">
                        <div><label className="text-xs font-semibold uppercase text-plomo">Año académico</label><select value={academicYearId} onChange={(e) => setAcademicYearId(e.target.value)} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm">{catalog.academic_years.map((y) => <option key={y.value} value={y.value}>{y.label}</option>)}</select></div>
                        <div><label className="text-xs font-semibold uppercase text-plomo">Fecha</label><input type="date" value={date} onChange={(e) => setDate(e.target.value)} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm" /></div>
                        <div><label className="text-xs font-semibold uppercase text-plomo">Nivel</label><select value={levelId} onChange={(e) => { setLevelId(e.target.value); setGradeId(''); setSectionId(''); }} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Seleccionar</option>{catalog.levels.map((l) => <option key={l.id} value={l.id}>{l.name}</option>)}</select></div>
                        <div><label className="text-xs font-semibold uppercase text-plomo">Grado</label><select value={gradeId} onChange={(e) => { setGradeId(e.target.value); setSectionId(''); }} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Seleccionar</option>{grades.map((g) => <option key={g.id} value={g.id}>{g.name}</option>)}</select></div>
                        <div><label className="text-xs font-semibold uppercase text-plomo">Sección</label><select value={sectionId} onChange={(e) => setSectionId(e.target.value)} className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"><option value="">Seleccionar</option>{sections.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}</select></div>
                        <div className="flex items-end"><button type="button" onClick={loadStudents} className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"><Search className="h-4 w-4" />Cargar estudiantes</button></div>
                    </div>
                </Card>

                {batch ? (
                    <Card>
                        {batch.students.length === 0 ? (
                            <p className="text-sm font-medium text-amber-700">
                                No hay estudiantes matriculados en esta sección para el año académico seleccionado.
                            </p>
                        ) : null}
                        <form
                            onSubmit={(e) => {
                                e.preventDefault();
                                router.post(route('teacher.attendance.store'), {
                                    ...form.data,
                                    academic_year_id: academicYearId,
                                    educational_level_id: levelId,
                                    grade_id: gradeId,
                                    section_id: sectionId,
                                    attendance_date: date,
                                });
                            }}
                        >
                            <div className="space-y-3">
                                {batch.students.map((student, idx) => (
                                    <div key={student.id} className="grid gap-3 rounded-lg border border-plomo/10 p-3 md:grid-cols-[2fr_1fr_2fr]">
                                        <div>
                                            <p className="font-semibold text-navy-900">{student.last_name}, {student.first_name}</p>
                                            <p className="text-xs text-plomo">{student.code} · {student.document_number ?? 'Sin documento'}</p>
                                        </div>
                                        <div>
                                            <div className="flex items-center gap-2">
                                                <span className={`inline-flex rounded-full p-2 ${statusClass(form.data.entries[idx]?.status ?? 'presente')}`}>
                                                    {statusIcon(form.data.entries[idx]?.status ?? 'presente')}
                                                </span>
                                                <select value={form.data.entries[idx]?.status ?? 'presente'} onChange={(e) => form.setData('entries', form.data.entries.map((entry, i) => i === idx ? { ...entry, status: e.target.value } : entry))} className={`w-full rounded-md border border-plomo/20 px-3 py-2 text-sm font-semibold ${statusClass(form.data.entries[idx]?.status ?? 'presente')}`}>
                                                    {catalog.statuses.map((status) => <option key={status.value} value={status.value}>{status.label}</option>)}
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <input type="text" value={form.data.entries[idx]?.observation ?? ''} onChange={(e) => form.setData('entries', form.data.entries.map((entry, i) => i === idx ? { ...entry, observation: e.target.value } : entry))} placeholder="Observación (opcional)" className="w-full rounded-md border border-plomo/20 px-3 py-2 text-sm" />
                                        </div>
                                    </div>
                                ))}
                            </div>
                            {batch.students.length > 0 ? (
                                <div className="mt-4 flex justify-end">
                                    <button type="submit" disabled={form.processing} className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">
                                        Guardar asistencia
                                    </button>
                                </div>
                            ) : null}
                        </form>
                    </Card>
                ) : (
                    <Card><p className="text-sm text-plomo">Seleccione sección y fecha, luego pulse «Cargar estudiantes».</p></Card>
                )}
            </PageContainer>
        </TeacherLayout>
    );
}

