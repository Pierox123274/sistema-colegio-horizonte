import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';

type Brief = { id: number; name: string; year?: number };

type AssignmentRow = {
    id: number;
    user?: { name: string; email: string };
    academic_year?: Brief;
    educational_level?: { name: string };
    grade?: { name: string };
    section?: { name: string };
    subject?: { name: string } | null;
    is_tutor: boolean;
    is_active: boolean;
};

type Props = PageProps<{
    assignments: {
        data: AssignmentRow[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: { user_id: string; academic_year_id: string };
    catalog: { teachers: SelectOption[]; academic_years: SelectOption[] };
}>;

export default function TeacherAssignmentsIndex() {
    const { assignments, filters, catalog } = usePage<Props>().props;

    return (
        <IntranetLayout title="Asignaciones docentes">
            <Head title="Administración — Asignaciones docentes" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Administración' },
                        { label: 'Asignaciones docentes' },
                    ]}
                />
                <SectionTitle
                    title="Asignaciones docentes"
                    description="Vincule docentes a año, nivel, grado, sección y curso opcional. El tutor de aula marca encargado."
                    actions={
                        <Link
                            href={route('intranet.admin.teacher-assignments.create')}
                            className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm"
                        >
                            Nueva asignación
                        </Link>
                    }
                />

                <Card className="mb-4">
                    <form
                        className="flex flex-wrap gap-3"
                        onSubmit={(e) => {
                            e.preventDefault();
                            const fd = new FormData(e.currentTarget);
                            router.get(
                                route('intranet.admin.teacher-assignments.index'),
                                Object.fromEntries(fd),
                                { preserveState: true },
                            );
                        }}
                    >
                        <select
                            name="user_id"
                            defaultValue={filters.user_id}
                            className="min-w-[220px] rounded-md border border-plomo/20 px-3 py-2 text-sm"
                        >
                            <option value="">Todos los docentes</option>
                            {catalog.teachers.map((t) => (
                                <option key={t.value} value={t.value}>
                                    {t.label}
                                </option>
                            ))}
                        </select>
                        <select
                            name="academic_year_id"
                            defaultValue={filters.academic_year_id}
                            className="min-w-[200px] rounded-md border border-plomo/20 px-3 py-2 text-sm"
                        >
                            <option value="">Todos los años</option>
                            {catalog.academic_years.map((y) => (
                                <option key={y.value} value={y.value}>
                                    {y.label}
                                </option>
                            ))}
                        </select>
                        <button
                            type="submit"
                            className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900"
                        >
                            Filtrar
                        </button>
                    </form>
                </Card>

                <Card>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b border-plomo/15 text-left">
                                    <th className="px-3 py-2">Docente</th>
                                    <th className="px-3 py-2">Año</th>
                                    <th className="px-3 py-2">Ubicación</th>
                                    <th className="px-3 py-2">Curso</th>
                                    <th className="px-3 py-2">Tutor</th>
                                    <th className="px-3 py-2">Estado</th>
                                    <th className="px-3 py-2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {assignments.data.map((a) => (
                                    <tr key={a.id} className="border-b border-plomo/10">
                                        <td className="px-3 py-2">
                                            <div className="font-medium text-navy-900">
                                                {a.user?.name ?? '—'}
                                            </div>
                                            <div className="text-xs text-plomo">{a.user?.email}</div>
                                        </td>
                                        <td className="px-3 py-2 text-plomo">
                                            {a.academic_year
                                                ? `${a.academic_year.name} (${a.academic_year.year})`
                                                : '—'}
                                        </td>
                                        <td className="px-3 py-2 text-plomo">
                                            {[a.educational_level?.name, a.grade?.name, a.section?.name]
                                                .filter(Boolean)
                                                .join(' · ')}
                                        </td>
                                        <td className="px-3 py-2 text-plomo">
                                            {a.subject?.name ?? '—'}
                                        </td>
                                        <td className="px-3 py-2">{a.is_tutor ? 'Sí' : 'No'}</td>
                                        <td className="px-3 py-2">
                                            <span
                                                className={`rounded-full px-2 py-1 text-xs font-semibold ${
                                                    a.is_active
                                                        ? 'bg-emerald-100 text-emerald-700'
                                                        : 'bg-red-100 text-red-700'
                                                }`}
                                            >
                                                {a.is_active ? 'Activa' : 'Inactiva'}
                                            </span>
                                        </td>
                                        <td className="px-3 py-2">
                                            <Link
                                                href={route(
                                                    'intranet.admin.teacher-assignments.edit',
                                                    a.id,
                                                )}
                                                className="text-xs font-semibold text-navy-900 underline"
                                            >
                                                Editar
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    {assignments.links && assignments.links.length > 3 ? (
                        <nav className="mt-4 flex flex-wrap justify-center gap-1">
                            {assignments.links.map((link, i) =>
                                link.url ? (
                                    <button
                                        key={i}
                                        type="button"
                                        className={`rounded-md px-3 py-1.5 text-sm ${
                                            link.active
                                                ? 'bg-navy-900 font-semibold text-white'
                                                : 'border border-plomo/20 bg-white'
                                        }`}
                                        onClick={() => router.visit(link.url!)}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ) : (
                                    <span
                                        key={i}
                                        className="px-3 py-1.5 text-sm text-plomo"
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ),
                            )}
                        </nav>
                    ) : null}
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
