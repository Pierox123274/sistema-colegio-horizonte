import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type RiskRow = {
    student: { id: number; code: string; full_name: string };
    section?: { id: number; name: string } | null;
    risk: {
        score: number;
        level: string;
        average: number | null;
        attendance_pct: number | null;
        flags: string[];
    };
};

type Props = PageProps<{
    risk_rows: RiskRow[];
}>;

export default function AcademicRisk() {
    const { risk_rows } = usePage<Props>().props;

    return (
        <TeacherLayout title="Riesgo académico">
            <Head title="Riesgo académico" />
            <PageContainer>
                <SectionTitle
                    title="Riesgo académico"
                    description="Estudiantes con bajo rendimiento, ausentismo relevante o nivel diagnóstico bajo. Combine con el panel pedagógico y diagnósticos para planificar refuerzo."
                />

                <Card>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-left text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 text-xs uppercase text-plomo">
                                    <th className="py-3 pr-4 font-semibold">Estudiante</th>
                                    <th className="py-3 pr-4 font-semibold">Sección</th>
                                    <th className="py-3 pr-4 font-semibold">Nivel / puntaje</th>
                                    <th className="py-3 pr-4 font-semibold">Señales</th>
                                    <th className="py-3 font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {risk_rows.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="py-6 text-center text-plomo">
                                            No hay estudiantes en riesgo según sus asignaciones o aún no hay datos.
                                        </td>
                                    </tr>
                                ) : (
                                    risk_rows.map((row) => (
                                        <tr key={row.student.id} className="border-b border-slate-100">
                                            <td className="py-3 pr-4">
                                                <span className="font-medium text-navy-900">{row.student.full_name}</span>
                                                <span className="ml-2 text-plomo">({row.student.code})</span>
                                            </td>
                                            <td className="py-3 pr-4 text-plomo">{row.section?.name ?? '—'}</td>
                                            <td className="py-3 pr-4">
                                                <span className="mr-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold capitalize text-navy-900">
                                                    {row.risk.level}
                                                </span>
                                                <span className="text-plomo">{row.risk.score}/100</span>
                                            </td>
                                            <td className="py-3 pr-4">
                                                <div className="flex flex-wrap gap-1">
                                                    {row.risk.flags.length === 0 ? (
                                                        <span className="text-xs text-plomo">—</span>
                                                    ) : (
                                                        row.risk.flags.map((s) => (
                                                            <span
                                                                key={s}
                                                                className="rounded-full bg-rose-50 px-2 py-0.5 text-xs font-medium text-rose-800"
                                                            >
                                                                {s.replaceAll('_', ' ')}
                                                            </span>
                                                        ))
                                                    )}
                                                </div>
                                            </td>
                                            <td className="py-3">
                                                <Link
                                                    href={route('teacher.students.show', row.student.id)}
                                                    className="text-sm font-medium text-brand-navy hover:underline"
                                                >
                                                    Ver ficha
                                                </Link>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </Card>
            </PageContainer>
        </TeacherLayout>
    );
}
