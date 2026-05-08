import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import type { FormEventHandler } from 'react';

const inputClass =
    'mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900';

type P = PageProps<{
    catalog: {
        categories: SelectOption[];
        product_types: SelectOption[];
        sizes: SelectOption[];
        gender_targets: SelectOption[];
    };
}>;

export default function InventoryProductCreate() {
    const { catalog } = usePage<P>().props;
    const form = useForm({
        product_category_id: catalog.categories[0]?.value ?? '',
        code: '',
        name: '',
        description: '',
        product_type: 'otro',
        size: 'unico',
        color: '',
        gender_target: 'no_aplica',
        unit: 'unidad',
        purchase_price: '0.00',
        sale_price: '0.00',
        current_stock: '0.00',
        minimum_stock: '0.00',
        is_active: true,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.post(route('intranet.inventory.products.store'));
    };

    return (
        <IntranetLayout title="Nuevo producto">
            <Head title="Nuevo producto - Inventario" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Inventario' },
                        {
                            label: 'Productos',
                            href: route('intranet.inventory.products.index'),
                        },
                        { label: 'Nuevo' },
                    ]}
                />
                <SectionTitle
                    title="Nuevo producto"
                    description="Registre un producto para el control de inventario."
                    actions={
                        <Link
                            href={route('intranet.inventory.products.index')}
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
                                <InputLabel
                                    htmlFor="product_category_id"
                                    value="Categoría *"
                                />
                                <select
                                    id="product_category_id"
                                    className={inputClass}
                                    value={form.data.product_category_id}
                                    onChange={(e) =>
                                        form.setData(
                                            'product_category_id',
                                            e.target.value,
                                        )
                                    }
                                >
                                    {catalog.categories.map((c) => (
                                        <option key={c.value} value={c.value}>
                                            {c.label}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.product_category_id}
                                    className="mt-1"
                                />
                            </div>
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
                            <div>
                                <InputLabel htmlFor="product_type" value="Tipo *" />
                                <select
                                    id="product_type"
                                    className={inputClass}
                                    value={form.data.product_type}
                                    onChange={(e) =>
                                        form.setData(
                                            'product_type',
                                            e.target.value,
                                        )
                                    }
                                >
                                    {catalog.product_types.map((item) => (
                                        <option
                                            key={item.value}
                                            value={item.value}
                                        >
                                            {item.label}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.product_type}
                                    className="mt-1"
                                />
                            </div>
                            <div>
                                <InputLabel htmlFor="size" value="Talla *" />
                                <select
                                    id="size"
                                    className={inputClass}
                                    value={form.data.size}
                                    onChange={(e) =>
                                        form.setData('size', e.target.value)
                                    }
                                >
                                    {catalog.sizes.map((item) => (
                                        <option
                                            key={item.value}
                                            value={item.value}
                                        >
                                            {item.label}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.size}
                                    className="mt-1"
                                />
                            </div>
                            <div>
                                <InputLabel htmlFor="color" value="Color" />
                                <TextInput
                                    id="color"
                                    className={inputClass}
                                    value={form.data.color}
                                    onChange={(e) =>
                                        form.setData('color', e.target.value)
                                    }
                                />
                                <InputError
                                    message={form.errors.color}
                                    className="mt-1"
                                />
                            </div>
                            <div>
                                <InputLabel
                                    htmlFor="gender_target"
                                    value="Dirigido a *"
                                />
                                <select
                                    id="gender_target"
                                    className={inputClass}
                                    value={form.data.gender_target}
                                    onChange={(e) =>
                                        form.setData(
                                            'gender_target',
                                            e.target.value,
                                        )
                                    }
                                >
                                    {catalog.gender_targets.map((item) => (
                                        <option
                                            key={item.value}
                                            value={item.value}
                                        >
                                            {item.label}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.gender_target}
                                    className="mt-1"
                                />
                            </div>
                            <div>
                                <InputLabel htmlFor="unit" value="Unidad *" />
                                <TextInput
                                    id="unit"
                                    className={inputClass}
                                    value={form.data.unit}
                                    onChange={(e) =>
                                        form.setData('unit', e.target.value)
                                    }
                                />
                                <InputError
                                    message={form.errors.unit}
                                    className="mt-1"
                                />
                            </div>
                            <div>
                                <InputLabel
                                    htmlFor="purchase_price"
                                    value="Precio compra *"
                                />
                                <TextInput
                                    id="purchase_price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    className={inputClass}
                                    value={form.data.purchase_price}
                                    onChange={(e) =>
                                        form.setData(
                                            'purchase_price',
                                            e.target.value,
                                        )
                                    }
                                />
                            </div>
                            <div>
                                <InputLabel
                                    htmlFor="sale_price"
                                    value="Precio venta *"
                                />
                                <TextInput
                                    id="sale_price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    className={inputClass}
                                    value={form.data.sale_price}
                                    onChange={(e) =>
                                        form.setData('sale_price', e.target.value)
                                    }
                                />
                            </div>
                            <div>
                                <InputLabel
                                    htmlFor="current_stock"
                                    value="Stock actual *"
                                />
                                <TextInput
                                    id="current_stock"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    className={inputClass}
                                    value={form.data.current_stock}
                                    onChange={(e) =>
                                        form.setData(
                                            'current_stock',
                                            e.target.value,
                                        )
                                    }
                                />
                            </div>
                            <div>
                                <InputLabel
                                    htmlFor="minimum_stock"
                                    value="Stock mínimo *"
                                />
                                <TextInput
                                    id="minimum_stock"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    className={inputClass}
                                    value={form.data.minimum_stock}
                                    onChange={(e) =>
                                        form.setData(
                                            'minimum_stock',
                                            e.target.value,
                                        )
                                    }
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
                                    Activo
                                </label>
                            </div>
                        </div>
                        <div className="flex justify-end">
                            <PrimaryButton disabled={form.processing}>
                                Guardar producto
                            </PrimaryButton>
                        </div>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}

