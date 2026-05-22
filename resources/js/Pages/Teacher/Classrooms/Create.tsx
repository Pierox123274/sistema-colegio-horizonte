import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';

type AssignmentRow = {
    section_id: number;
    subject_id: number | null;
    academic_year_id: number;
    section?: string;
    subject?: string;
    academic_year?: string;
};

type Props = PageProps<{ assignments: AssignmentRow[] }>;

export default function TeacherClassroomsCreate() {
    const { assignments } = usePage<Props>().props;
    const first = assignments[0];

    const form = useForm({
        title: '',
        description: '',
        academic_year_id: first?.academic_year_id ?? '',
        section_id: first?.section_id ?? '',
        subject_id: first?.subject_id ?? '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(route('teacher.classrooms.store'));
    };

    return (
        <TeacherLayout title="Nueva aula">
            <Head title="Nueva aula virtual" />
            <PageContainer>
                <SectionTitle title="Crear aula virtual" description="Debe coincidir con una asignación activa." />
                <Card>
                    <form onSubmit={submit} className="mx-auto max-w-lg space-y-4">
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
                            <label className="block text-sm font-medium">Asignación (sección / curso)</label>
                            <select
                                className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                onChange={(e) => {
                                    const a = assignments[Number(e.target.value)];
                                    if (a) {
                                        form.setData({
                                            ...form.data,
                                            academic_year_id: a.academic_year_id,
                                            section_id: a.section_id,
                                            subject_id: a.subject_id ?? '',
                                        });
                                    }
                                }}
                            >
                                {assignments.map((a, i) => (
                                    <option key={i} value={i}>
                                        {a.subject} — {a.section} ({a.academic_year})
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="flex gap-3">
                            <button
                                type="submit"
                                disabled={form.processing}
                                className="rounded-lg bg-brand-navy px-4 py-2 text-sm font-semibold text-white disabled:opacity-50"
                            >
                                Crear
                            </button>
                            <Link href={route('teacher.classrooms.index')} className="rounded-lg border px-4 py-2 text-sm">
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </Card>
            </PageContainer>
        </TeacherLayout>
    );
}
