import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { AlertTriangle } from 'lucide-react';

type RiskRow = {
    student: { id: number; code: string; full_name: string };
    section: { id: number; name: string } | null | undefined;
    risk: {
        level: string;
        score: number;
        average: number | null;
        attendance_pct: number | null;
    };
};

type Props = PageProps<{
    risk_rows: RiskRow[];
}>;

const levelBadge = (level: string) => {
    if (level === 'alto') {
        return 'bg-red-100 text-red-800';
    }
    if (level === 'medio') {
        return 'bg-amber-100 text-amber-900';
    }
    return 'bg-emerald-100 text-emerald-900';
};

export default function StudentsRisk() {
    const { risk_rows } = usePage<Props>().props;

    return (
        <TeacherLayout title="IA — Alumnos en riesgo">
            <Head title="Alumnos en riesgo" />
            <PageContainer>
                <SectionTitle
                    title="Alumnos en riesgo académico"
                    description="Clasificación heurística según notas y asistencia registradas en el sistema. Use como apoyo, no como etiqueta definitiva."
                    actions={
                        <Link
                            href={route('teacher.ai-insights.index')}
                            className="text-sm font-semibold text-navy underline"
                        >
                            Ver resumen IA
                        </Link>
                    }
                />

                <Card className="overflow-x-auto">
                    <table className="min-w-full text-left text-sm">
                        <thead>
                            <tr className="border-b border-slate-200 text-xs uppercase text-plomo">
                                <th className="py-2 pr-4">Estudiante</th>
                                <th className="py-2 pr-4">Sección</th>
                                <th className="py-2 pr-4">Nivel</th>
                                <th className="py-2 pr-4">Promedio</th>
                                <th className="py-2 pr-4">Asist. %</th>
                                <th className="py-2">Puntos</th>
                            </tr>
                        </thead>
                        <tbody>
                            {risk_rows.length === 0 ? (
                                <tr>
                                    <td colSpan={6} className="py-8 text-center text-plomo">
                                        <AlertTriangle className="mx-auto mb-2 h-6 w-6 text-slate-300" />
                                        Sin estudiantes en sus secciones para el año activo, o sin matrículas
                                        matriculadas.
                                    </td>
                                </tr>
                            ) : (
                                risk_rows.map((row: RiskRow) => (
                                    <tr key={row.student.id} className="border-b border-slate-100">
                                        <td className="py-2 pr-4 font-medium text-navy">
                                            {row.student.full_name}
                                            <span className="ml-1 text-xs text-plomo">{row.student.code}</span>
                                        </td>
                                        <td className="py-2 pr-4">{row.section?.name ?? '—'}</td>
                                        <td className="py-2 pr-4">
                                            <span
                                                className={`inline-flex rounded px-2 py-0.5 text-xs font-semibold capitalize ${levelBadge(row.risk.level)}`}
                                            >
                                                {row.risk.level}
                                            </span>
                                        </td>
                                        <td className="py-2 pr-4">{row.risk.average ?? '—'}</td>
                                        <td className="py-2 pr-4">
                                            {row.risk.attendance_pct != null
                                                ? `${row.risk.attendance_pct}%`
                                                : '—'}
                                        </td>
                                        <td className="py-2 font-mono text-xs">{row.risk.score}</td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </Card>
            </PageContainer>
        </TeacherLayout>
    );
}
