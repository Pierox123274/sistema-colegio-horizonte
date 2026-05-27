import { AppBadge } from '@/Components/App/AppBadge';
import { AppEmptyState } from '@/Components/App/AppEmptyState';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { AppTable } from '@/Components/App/AppTable';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { BrainCircuit } from 'lucide-react';

type Row = {
    id: number;
    title: string;
    mode: string;
    is_active: boolean;
    subject: { name: string } | null;
    academic_year: string | null;
    section: string | null;
    grade: string | null;
    attempts_count: number;
    questions_count: number;
};

type Props = PageProps<{
    exams: { data: Row[] };
    can_create: boolean;
}>;

export default function IntranetDiagnosticExamsIndex() {
    const { exams, can_create } = usePage<Props>().props;

    return (
        <IntranetLayout title="Diagnósticos">
            <Head title="Exámenes diagnóstico" />
            <PageContainer>
                <AppPageHeader
                    title="Exámenes diagnóstico"
                    description="Administración institucional: alcance por año, nivel, grado, sección y curso."
                    actions={
                        can_create ? (
                            <Link
                                href={route('intranet.adaptive.diagnostic-exams.create')}
                                className="inline-flex items-center justify-center rounded-lg bg-brand-navy px-4 py-2.5 text-sm font-semibold text-white app-transition hover:bg-navy-800"
                            >
                                Nuevo examen
                            </Link>
                        ) : null
                    }
                />
                <AppTable stickyHeader title="Diagnósticos configurados">
                    {exams.data.length === 0 ? (
                        <div className="p-4">
                            <AppEmptyState
                                icon={BrainCircuit}
                                title="Sin diagnósticos registrados"
                                description="Crea un examen para iniciar el seguimiento adaptativo institucional."
                            />
                        </div>
                    ) : (
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-left text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 text-xs uppercase text-plomo">
                                    <th className="py-3 pr-3 font-semibold">Título</th>
                                    <th className="py-3 pr-3 font-semibold">Año</th>
                                    <th className="py-3 pr-3 font-semibold">Curso</th>
                                    <th className="py-3 pr-3 font-semibold">Sección</th>
                                    <th className="py-3 pr-3 font-semibold">Modo</th>
                                    <th className="py-3 pr-3 font-semibold">Preg.</th>
                                    <th className="py-3 pr-3 font-semibold">Intentos</th>
                                    <th className="py-3 font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {exams.data.map((e) => (
                                    <tr key={e.id} className="border-b border-slate-100">
                                        <td className="py-3 pr-3 font-medium">{e.title}</td>
                                        <td className="py-3 pr-3 text-plomo">{e.academic_year ?? '—'}</td>
                                        <td className="py-3 pr-3 text-plomo">{e.subject?.name ?? '—'}</td>
                                        <td className="py-3 pr-3 text-plomo">{e.section ?? '—'}</td>
                                        <td className="py-3 pr-3 capitalize">
                                            <AppBadge tone={e.mode === 'adaptive' ? 'info' : 'neutral'}>
                                                {e.mode}
                                            </AppBadge>
                                        </td>
                                        <td className="py-3 pr-3">{e.questions_count}</td>
                                        <td className="py-3 pr-3">{e.attempts_count}</td>
                                        <td className="py-3">
                                            <Link
                                                href={route('intranet.adaptive.diagnostic-exams.show', e.id)}
                                                className="mr-3 font-medium text-brand-navy hover:underline"
                                            >
                                                Ver
                                            </Link>
                                            {can_create ? (
                                                <Link
                                                    href={route('intranet.adaptive.diagnostic-exams.edit', e.id)}
                                                    className="font-medium text-brand-navy hover:underline"
                                                >
                                                    Editar
                                                </Link>
                                            ) : null}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    )}
                </AppTable>
            </PageContainer>
        </IntranetLayout>
    );
}
