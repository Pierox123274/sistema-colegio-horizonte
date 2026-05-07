import { Card } from '@/Components/Intranet/Card';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import type { FormEventHandler } from 'react';

const inputClass =
    'mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900';

type P = PageProps<{
    catalog: { types: SelectOption[] };
}>;

export default function PaymentConceptsCreate() {
    const { catalog } = usePage<P>().props;
    const form = useForm({
        code: '',
        name: '',
        description: '',
        default_amount: '0',
        type: catalog.types[0]?.value ?? 'pension',
        is_active: true,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.post(route('intranet.payment-concepts.store'));
    };

    return (
        <IntranetLayout title="Nuevo concepto">
            <Head title="Nuevo concepto — Horizonte" />

            <PageContainer>
                <SectionTitle
                    title="Nuevo concepto de pago"
                    actions={
                        <Link
                            href={route('intranet.payment-concepts.index')}
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
                            <div>
                                <InputLabel
                                    htmlFor="default_amount"
                                    value="Monto referencial (S/) *"
                                />
                                <TextInput
                                    id="default_amount"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    className={inputClass}
                                    value={form.data.default_amount}
                                    onChange={(e) =>
                                        form.setData(
                                            'default_amount',
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputError
                                    message={form.errors.default_amount}
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
                                    {catalog.types.map((t) => (
                                        <option key={t.value} value={t.value}>
                                            {t.label}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.type}
                                    className="mt-1"
                                />
                            </div>
                            <div className="flex items-end pb-2">
                                <label className="flex cursor-pointer items-center gap-2 text-sm font-medium">
                                    <input
                                        type="checkbox"
                                        className="rounded border-plomo/30 text-navy-900 focus:ring-navy-900"
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
                        <PrimaryButton disabled={form.processing}>
                            Guardar
                        </PrimaryButton>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
