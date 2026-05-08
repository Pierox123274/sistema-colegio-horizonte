import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEventHandler } from 'react';

const inputClass =
    'mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900';

export default function InventoryCategoryCreate() {
    const form = useForm({
        code: '',
        name: '',
        description: '',
        is_active: true,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.post(route('intranet.inventory.categories.store'));
    };

    return (
        <IntranetLayout title="Nueva categoría">
            <Head title="Nueva categoría - Inventario" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Inventario' },
                        {
                            label: 'Categorías',
                            href: route('intranet.inventory.categories.index'),
                        },
                        { label: 'Nueva' },
                    ]}
                />
                <SectionTitle
                    title="Nueva categoría"
                    description="Registre un grupo de productos para el inventario."
                    actions={
                        <Link
                            href={route('intranet.inventory.categories.index')}
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
                                Guardar categoría
                            </PrimaryButton>
                        </div>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}

