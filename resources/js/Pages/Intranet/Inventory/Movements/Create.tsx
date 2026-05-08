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
import { useMemo } from 'react';

const inputClass =
    'mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900';

type ProductOption = {
    id: number;
    value: string;
    label: string;
    stock: string;
    minimum_stock: string;
    is_active: boolean;
    category?: string;
};

type P = PageProps<{
    catalog: {
        types: SelectOption[];
        products: ProductOption[];
    };
}>;

export default function InventoryMovementCreate() {
    const { catalog } = usePage<P>().props;
    const form = useForm({
        product_id: catalog.products[0]?.value ?? '',
        type: catalog.types[0]?.value ?? 'entrada',
        quantity: '',
        reason: '',
        observations: '',
    });

    const selectedProduct = useMemo(
        () =>
            catalog.products.find((p) => p.value === form.data.product_id) ??
            null,
        [catalog.products, form.data.product_id],
    );

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.post(route('intranet.inventory.movements.store'));
    };

    return (
        <IntranetLayout title="Nuevo movimiento">
            <Head title="Nuevo movimiento de inventario" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Inventario' },
                        {
                            label: 'Movimientos',
                            href: route('intranet.inventory.movements.index'),
                        },
                        { label: 'Nuevo' },
                    ]}
                />
                <SectionTitle
                    title="Registrar movimiento"
                    description="Entradas, salidas o ajustes con control de stock."
                    actions={
                        <Link
                            href={route('intranet.inventory.movements.index')}
                            className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                        >
                            Volver
                        </Link>
                    }
                />
                <Card>
                    <form onSubmit={submit} className="space-y-6">
                        <div className="grid gap-6 sm:grid-cols-2">
                            <div className="sm:col-span-2">
                                <InputLabel
                                    htmlFor="product_id"
                                    value="Producto *"
                                />
                                <select
                                    id="product_id"
                                    className={inputClass}
                                    value={form.data.product_id}
                                    onChange={(e) =>
                                        form.setData('product_id', e.target.value)
                                    }
                                >
                                    {catalog.products.map((p) => (
                                        <option key={p.id} value={p.value}>
                                            {p.label}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.product_id}
                                    className="mt-1"
                                />
                            </div>

                            <div>
                                <InputLabel htmlFor="type" value="Tipo *" />
                                <select
                                    id="type"
                                    className={inputClass}
                                    value={form.data.type}
                                    onChange={(e) =>
                                        form.setData('type', e.target.value)
                                    }
                                >
                                    {catalog.types.map((type) => (
                                        <option
                                            key={type.value}
                                            value={type.value}
                                        >
                                            {type.label}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.type}
                                    className="mt-1"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    htmlFor="quantity"
                                    value={
                                        form.data.type === 'ajuste'
                                            ? 'Stock final (ajuste) *'
                                            : 'Cantidad *'
                                    }
                                />
                                <TextInput
                                    id="quantity"
                                    type="number"
                                    min="0.01"
                                    step="0.01"
                                    className={inputClass}
                                    value={form.data.quantity}
                                    onChange={(e) =>
                                        form.setData('quantity', e.target.value)
                                    }
                                />
                                <InputError
                                    message={form.errors.quantity}
                                    className="mt-1"
                                />
                            </div>

                            <div className="sm:col-span-2">
                                <InputLabel htmlFor="reason" value="Motivo *" />
                                <TextInput
                                    id="reason"
                                    className={inputClass}
                                    value={form.data.reason}
                                    onChange={(e) =>
                                        form.setData('reason', e.target.value)
                                    }
                                />
                                <InputError
                                    message={form.errors.reason}
                                    className="mt-1"
                                />
                            </div>

                            <div className="sm:col-span-2">
                                <InputLabel
                                    htmlFor="observations"
                                    value="Observaciones"
                                />
                                <textarea
                                    id="observations"
                                    rows={3}
                                    className={inputClass}
                                    value={form.data.observations}
                                    onChange={(e) =>
                                        form.setData(
                                            'observations',
                                            e.target.value,
                                        )
                                    }
                                />
                            </div>
                        </div>

                        {selectedProduct ? (
                            <div className="rounded-lg border border-plomo/15 bg-navy-50/60 px-4 py-3 text-sm text-navy-900">
                                Stock actual:{' '}
                                <strong>
                                    {Number(selectedProduct.stock).toFixed(2)}
                                </strong>{' '}
                                | Stock mínimo:{' '}
                                <strong>
                                    {Number(selectedProduct.minimum_stock).toFixed(
                                        2,
                                    )}
                                </strong>
                            </div>
                        ) : null}

                        <div className="flex justify-end">
                            <PrimaryButton disabled={form.processing}>
                                Guardar movimiento
                            </PrimaryButton>
                        </div>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}

