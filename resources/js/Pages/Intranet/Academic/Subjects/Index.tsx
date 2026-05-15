import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';

type SubjectRow = {
    id: number;
    code: string;
    name: string;
    description: string | null;
    is_active: boolean;
};

type Props = PageProps<{
    subjects: {
        data: SubjectRow[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: { search: string; is_active: string };
}>;

export default function SubjectIndex() {
    const { subjects, filters } = usePage<Props>().props;

    return (
        <IntranetLayout title="Cursos">
            <Head title="Cursos" />
            <PageContainer>
                <IntranetBreadcrumbs items={[{ label: 'Gestión académica' }, { label: 'Cursos' }]} />
                <SectionTitle
                    title="Cursos / asignaturas"
                    description="Catálogo académico institucional."
                    actions={<Link href={route('intranet.academic.subjects.create')} className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white">Nuevo curso</Link>}
                />
                <Card>
                    <div className="mb-4 grid gap-3 md:grid-cols-3">
                        <input
                            defaultValue={filters.search}
                            onBlur={(e) => router.get(route('intranet.academic.subjects.index'), { ...filters, search: e.target.value }, { preserveState: true })}
                            placeholder="Buscar por código o nombre"
                            className="rounded-md border border-plomo/20 px-3 py-2 text-sm"
                        />
                        <select
                            defaultValue={filters.is_active}
                            onChange={(e) => router.get(route('intranet.academic.subjects.index'), { ...filters, is_active: e.target.value }, { preserveState: true })}
                            className="rounded-md border border-plomo/20 px-3 py-2 text-sm"
                        >
                            <option value="">Todos</option>
                            <option value="1">Activos</option>
                            <option value="0">Inactivos</option>
                        </select>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b border-plomo/15 text-left">
                                    <th className="px-3 py-2">Código</th>
                                    <th className="px-3 py-2">Curso</th>
                                    <th className="px-3 py-2">Estado</th>
                                    <th className="px-3 py-2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {subjects.data.map((subject) => (
                                    <tr key={subject.id} className="border-b border-plomo/10">
                                        <td className="px-3 py-2">{subject.code}</td>
                                        <td className="px-3 py-2">
                                            <p className="font-semibold text-navy-900">{subject.name}</p>
                                            <p className="text-xs text-plomo">{subject.description ?? 'Sin descripción'}</p>
                                        </td>
                                        <td className="px-3 py-2">
                                            <span className={`rounded-full px-2 py-1 text-xs font-semibold ${subject.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'}`}>
                                                {subject.is_active ? 'Activo' : 'Inactivo'}
                                            </span>
                                        </td>
                                        <td className="px-3 py-2">
                                            <div className="flex gap-2">
                                                <Link href={route('intranet.academic.subjects.show', subject.id)} className="rounded border border-plomo/20 px-2 py-1 text-xs font-semibold">Ver</Link>
                                                <Link href={route('intranet.academic.subjects.edit', subject.id)} className="rounded border border-plomo/20 px-2 py-1 text-xs font-semibold">Editar</Link>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}

