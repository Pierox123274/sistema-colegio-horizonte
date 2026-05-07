import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';

type ClassroomModel = {
    id: number;
    section_id: number | null;
    code: string;
    name: string;
    floor: string | null;
    capacity: number;
    description: string | null;
    is_active: boolean;
};

type EditPageProps = PageProps<{
    classroom: ClassroomModel;
    catalog: { sections: SelectOption[] };
}>;

export default function ClassroomsEdit() {
    const { classroom, catalog } = usePage<EditPageProps>().props;

    const form = useForm({
        section_id:
            classroom.section_id !== null
                ? String(classroom.section_id)
                : '',
        code: classroom.code,
        name: classroom.name,
        floor: classroom.floor ?? '',
        capacity: classroom.capacity,
        description: classroom.description ?? '',
        is_active: classroom.is_active,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.put(route('intranet.academic.classrooms.update', classroom.id));
    };

    return (
        <IntranetLayout title="Editar aula">
            <Head title={`Editar ${classroom.name} — Horizonte`} />

            <PageContainer width="default">
                <IntranetBreadcrumbs
                    items={[
                        {
                            label: 'Aulas',
                            href: route('intranet.academic.classrooms.index'),
                        },
                        {
                            label: classroom.name,
                            href: route(
                                'intranet.academic.classrooms.show',
                                classroom.id,
                            ),
                        },
                        { label: 'Editar' },
                    ]}
                />

                <SectionTitle
                    title="Editar aula"
                    description="Capacidad debe ser mayor que cero."
                />

                <Card>
                    <form onSubmit={submit} className="space-y-5">
                        <div>
                            <label
                                htmlFor="section_id"
                                className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                            >
                                Sección (opcional)
                            </label>
                            <select
                                id="section_id"
                                value={form.data.section_id}
                                onChange={(e) =>
                                    form.setData('section_id', e.target.value)
                                }
                                className="mt-1 w-full max-w-lg rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            >
                                <option value="">Sin sección</option>
                                {catalog.sections.map((o) => (
                                    <option key={o.value} value={o.value}>
                                        {o.label}
                                    </option>
                                ))}
                            </select>
                            {form.errors.section_id ? (
                                <p className="mt-1 text-xs text-brand-red">
                                    {form.errors.section_id}
                                </p>
                            ) : null}
                        </div>

                        <div>
                            <label
                                htmlFor="code"
                                className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                            >
                                Código *
                            </label>
                            <input
                                id="code"
                                type="text"
                                value={form.data.code}
                                onChange={(e) =>
                                    form.setData('code', e.target.value)
                                }
                                required
                                className="mt-1 w-full max-w-md rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            />
                            {form.errors.code ? (
                                <p className="mt-1 text-xs text-brand-red">
                                    {form.errors.code}
                                </p>
                            ) : null}
                        </div>

                        <div>
                            <label
                                htmlFor="name"
                                className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                            >
                                Nombre *
                            </label>
                            <input
                                id="name"
                                type="text"
                                value={form.data.name}
                                onChange={(e) =>
                                    form.setData('name', e.target.value)
                                }
                                required
                                className="mt-1 w-full max-w-md rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            />
                            {form.errors.name ? (
                                <p className="mt-1 text-xs text-brand-red">
                                    {form.errors.name}
                                </p>
                            ) : null}
                        </div>

                        <div>
                            <label
                                htmlFor="floor"
                                className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                            >
                                Piso
                            </label>
                            <input
                                id="floor"
                                type="text"
                                value={form.data.floor}
                                onChange={(e) =>
                                    form.setData('floor', e.target.value)
                                }
                                className="mt-1 w-full max-w-[120px] rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            />
                            {form.errors.floor ? (
                                <p className="mt-1 text-xs text-brand-red">
                                    {form.errors.floor}
                                </p>
                            ) : null}
                        </div>

                        <div>
                            <label
                                htmlFor="capacity"
                                className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                            >
                                Capacidad *
                            </label>
                            <input
                                id="capacity"
                                type="number"
                                min={1}
                                value={form.data.capacity}
                                onChange={(e) =>
                                    form.setData(
                                        'capacity',
                                        Number(e.target.value) || 1,
                                    )
                                }
                                required
                                className="mt-1 w-full max-w-[140px] rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            />
                            {form.errors.capacity ? (
                                <p className="mt-1 text-xs text-brand-red">
                                    {form.errors.capacity}
                                </p>
                            ) : null}
                        </div>

                        <div>
                            <label
                                htmlFor="description"
                                className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                            >
                                Descripción
                            </label>
                            <textarea
                                id="description"
                                value={form.data.description}
                                onChange={(e) =>
                                    form.setData('description', e.target.value)
                                }
                                rows={3}
                                className="mt-1 w-full max-w-xl rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            />
                            {form.errors.description ? (
                                <p className="mt-1 text-xs text-brand-red">
                                    {form.errors.description}
                                </p>
                            ) : null}
                        </div>

                        <label className="flex cursor-pointer items-center gap-2">
                            <input
                                type="checkbox"
                                checked={form.data.is_active}
                                onChange={(e) =>
                                    form.setData('is_active', e.target.checked)
                                }
                                className="rounded border-plomo/30 text-navy-900 focus:ring-navy-900"
                            />
                            <span className="text-sm text-navy-900">
                                Aula activa
                            </span>
                        </label>

                        <div className="flex flex-wrap gap-3 pt-2">
                            <button
                                type="submit"
                                disabled={form.processing}
                                className="inline-flex items-center rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950 disabled:opacity-50"
                            >
                                Actualizar
                            </button>
                            <Link
                                href={route(
                                    'intranet.academic.classrooms.show',
                                    classroom.id,
                                )}
                                className="inline-flex items-center rounded-lg border border-plomo/20 bg-white px-4 py-2 text-sm font-semibold text-navy-900 shadow-sm transition hover:bg-navy-50"
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
