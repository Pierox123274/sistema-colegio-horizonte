import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';

type CreatePageProps = PageProps<{
    catalog: { grades: SelectOption[] };
}>;

export default function SectionsCreate() {
    const { catalog } = usePage<CreatePageProps>().props;

    const form = useForm({
        grade_id: catalog.grades[0]?.value ?? '',
        code: '',
        name: '',
        capacity: 25,
        is_active: true,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.post(route('intranet.academic.sections.store'));
    };

    return (
        <IntranetLayout title="Nueva sección">
            <Head title="Nueva sección — Horizonte" />

            <PageContainer width="default">
                <IntranetBreadcrumbs
                    items={[
                        {
                            label: 'Secciones',
                            href: route('intranet.academic.sections.index'),
                        },
                        { label: 'Nueva' },
                    ]}
                />

                <SectionTitle
                    title="Nueva sección"
                    description="El código es único dentro del grado elegido."
                />

                <Card>
                    <form onSubmit={submit} className="space-y-5">
                        <div>
                            <label
                                htmlFor="grade_id"
                                className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                            >
                                Grado *
                            </label>
                            <select
                                id="grade_id"
                                value={String(form.data.grade_id)}
                                onChange={(e) =>
                                    form.setData('grade_id', e.target.value)
                                }
                                required
                                className="mt-1 w-full max-w-lg rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            >
                                {catalog.grades.map((o) => (
                                    <option key={o.value} value={o.value}>
                                        {o.label}
                                    </option>
                                ))}
                            </select>
                            {form.errors.grade_id ? (
                                <p className="mt-1 text-xs text-brand-red">
                                    {form.errors.grade_id}
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
                                className="mt-1 w-full max-w-xs rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
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
                                Sección activa
                            </span>
                        </label>

                        <div className="flex flex-wrap gap-3 pt-2">
                            <button
                                type="submit"
                                disabled={form.processing}
                                className="inline-flex items-center rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-950 disabled:opacity-50"
                            >
                                Guardar
                            </button>
                            <Link
                                href={route('intranet.academic.sections.index')}
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
