import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function LevelsCreate() {
    const form = useForm({
        code: '',
        name: '',
        description: '',
        is_active: true,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.post(route('intranet.academic.levels.store'));
    };

    return (
        <IntranetLayout title="Nuevo nivel">
            <Head title="Nuevo nivel educativo — Horizonte" />

            <PageContainer width="default">
                <IntranetBreadcrumbs
                    items={[
                        {
                            label: 'Niveles educativos',
                            href: route('intranet.academic.levels.index'),
                        },
                        { label: 'Nuevo' },
                    ]}
                />

                <SectionTitle
                    title="Nuevo nivel educativo"
                    description="Defina el código corto (único), nombre y estado."
                />

                <Card>
                    <form onSubmit={submit} className="space-y-5">
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
                                Nivel activo
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
                                href={route('intranet.academic.levels.index')}
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
