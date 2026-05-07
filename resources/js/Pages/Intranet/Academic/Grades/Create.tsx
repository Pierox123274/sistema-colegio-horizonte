import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';

type CreatePageProps = PageProps<{
    catalog: { educational_levels: SelectOption[] };
}>;

export default function GradesCreate() {
    const { catalog } = usePage<CreatePageProps>().props;

    const form = useForm({
        educational_level_id:
            catalog.educational_levels[0]?.value ?? '',
        code: '',
        name: '',
        order: 1,
        is_active: true,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.post(route('intranet.academic.grades.store'));
    };

    return (
        <IntranetLayout title="Nuevo grado">
            <Head title="Nuevo grado — Horizonte" />

            <PageContainer width="default">
                <IntranetBreadcrumbs
                    items={[
                        {
                            label: 'Grados',
                            href: route('intranet.academic.grades.index'),
                        },
                        { label: 'Nuevo' },
                    ]}
                />

                <SectionTitle
                    title="Nuevo grado"
                    description="El código y el orden deben ser únicos dentro del nivel seleccionado."
                />

                <Card>
                    <form onSubmit={submit} className="space-y-5">
                        <div>
                            <label
                                htmlFor="educational_level_id"
                                className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                            >
                                Nivel educativo *
                            </label>
                            <select
                                id="educational_level_id"
                                value={String(form.data.educational_level_id)}
                                onChange={(e) =>
                                    form.setData(
                                        'educational_level_id',
                                        e.target.value,
                                    )
                                }
                                required
                                className="mt-1 w-full max-w-md rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            >
                                {catalog.educational_levels.map((o) => (
                                    <option key={o.value} value={o.value}>
                                        {o.label}
                                    </option>
                                ))}
                            </select>
                            {form.errors.educational_level_id ? (
                                <p className="mt-1 text-xs text-brand-red">
                                    {form.errors.educational_level_id}
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
                                htmlFor="order"
                                className="block text-xs font-semibold uppercase tracking-wide text-plomo"
                            >
                                Orden *
                            </label>
                            <input
                                id="order"
                                type="number"
                                min={1}
                                value={form.data.order}
                                onChange={(e) =>
                                    form.setData(
                                        'order',
                                        Number(e.target.value) || 1,
                                    )
                                }
                                required
                                className="mt-1 w-full max-w-[120px] rounded-md border border-plomo/20 bg-white px-3 py-2 text-sm text-navy-900 shadow-sm focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900"
                            />
                            {form.errors.order ? (
                                <p className="mt-1 text-xs text-brand-red">
                                    {form.errors.order}
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
                                Grado activo
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
                                href={route('intranet.academic.grades.index')}
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
