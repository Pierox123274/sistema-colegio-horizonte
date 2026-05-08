import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import type { FormEventHandler } from 'react';

const inputClass =
    'mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900';

type P = PageProps<{
    category: {
        id: number;
        code: string;
        name: string;
        description: string | null;
        is_active: boolean;
    };
}>;

export default function InventoryCategoryEdit() {
    const { category } = usePage<P>().props;
    const form = useForm({
        code: category.code,
        name: category.name,
        description: category.description ?? '',
        is_active: category.is_active,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.put(route('intranet.inventory.categories.update', category.id));
    };

    return (
        <IntranetLayout title={`Editar ${category.code}`}>
            <Head title={`Editar categoría ${category.code}`} />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Inventario' },
                        {
                            label: 'Categorías',
                            href: route('intranet.inventory.categories.index'),
                        },
                        {
                            label: category.code,
                            href: route(
                                'intranet.inventory.categories.show',
                                category.id,
                            ),
                        },
                        { label: 'Editar' },
                    ]}
                />
                <SectionTitle
                    title={`Editar ${category.code}`}
                    description="Actualice los datos de la categoría."
                    actions={
                        <Link
                            href={route(
                                'intranet.inventory.categories.show',
                                category.id,
                            )}
                            className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                        >
                            Volver
                        </Link>
                    }
                />
                <Card>
                    <form onSubmit={submit} className="space-y-6">
                        <div className="grid gap-6 sm:grid-cols-2">
                            <div>
                                <InputLabel htmlFor="code" value="Código *" />
                                <TextInput
                                    id="code"
                                    className={inputClass}
                                    value={form.data.code}
                                    onChange={(e) =>
                                        form.setData('code', e.target.value)
                                    }
                                />
                                <InputError
                                    message={form.errors.code}
                                    className="mt-1"
                                />
                            </div>
                            <div>
                                <InputLabel htmlFor="name" value="Nombre *" />
                                <TextInput
                                    id="name"
                                    className={inputClass}
                                    value={form.data.name}
                                    onChange={(e) =>
                                        form.setData('name', e.target.value)
                                    }
                                />
                                <InputError
                                    message={form.errors.name}
                                    className="mt-1"
                                />
                            </div>
                            <div className="sm:col-span-2">
                                <InputLabel
                                    htmlFor="description"
                                    value="Descripción"
                                />
                                <textarea
                                    id="description"
                                    rows={3}
                                    className={inputClass}
                                    value={form.data.description}
                                    onChange={(e) =>
                                        form.setData(
                                            'description',
                                            e.target.value,
                                        )
                                    }
                                />
                            </div>
                            <div className="sm:col-span-2">
                                <label className="inline-flex items-center gap-2 text-sm font-medium text-navy-900">
                                    <input
                                        type="checkbox"
                                        checked={form.data.is_active}
                                        onChange={(e) =>
                                            form.setData(
                                                'is_active',
                                                e.target.checked,
                                            )
                                        }
                                    />
                                    Activa
                                </label>
                            </div>
                        </div>
                        <div className="flex justify-end">
                            <PrimaryButton disabled={form.processing}>
                                Guardar cambios
                            </PrimaryButton>
                        </div>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}

