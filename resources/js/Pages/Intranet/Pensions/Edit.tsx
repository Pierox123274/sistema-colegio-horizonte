import { Card } from '@/Components/Intranet/Card';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import type { FormEventHandler } from 'react';

const inputClass =
    'mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900';

type ConceptOpt = SelectOption & { default_amount?: string };

type PensionRow = {
    id: number;
    enrollment_id: number;
    payment_concept_id: number;
    month: number;
    year: number;
    amount: string;
    due_date: string;
    status: string;
    observations: string | null;
};

type P = PageProps<{
    pension: PensionRow;
    catalog: {
        enrollments: SelectOption[];
        concepts: ConceptOpt[];
        statuses: SelectOption[];
    };
}>;

export default function PensionsEdit() {
    const { pension, catalog } = usePage<P>().props;

    const form = useForm({
        enrollment_id: String(pension.enrollment_id),
        payment_concept_id: String(pension.payment_concept_id),
        month: String(pension.month),
        year: String(pension.year),
        amount: pension.amount,
        due_date: pension.due_date?.slice(0, 10) ?? '',
        status: pension.status,
        observations: pension.observations ?? '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.put(route('intranet.pensions.update', pension.id));
    };

    return (
        <IntranetLayout title="Editar pensión">
            <Head title="Editar pensión — Horizonte" />

            <PageContainer>
                <SectionTitle
                    title="Editar pensión"
                    actions={
                        <div className="flex gap-4">
                            <Link
                                href={route(
                                    'intranet.pensions.show',
                                    pension.id,
                                )}
                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Ver
                            </Link>
                            <Link
                                href={route('intranet.pensions.index')}
                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Listado
                            </Link>
                        </div>
                    }
                />

                <Card>
                    <form onSubmit={submit} className="space-y-6">
                        <div className="grid gap-6 sm:grid-cols-2">
                            <div className="sm:col-span-2">
                                <InputLabel
                                    htmlFor="enrollment_id"
                                    value="Matrícula *"
                                />
                                <select
                                    id="enrollment_id"
                                    className={inputClass}
                                    value={form.data.enrollment_id}
                                    onChange={(e) =>
                                        form.setData(
                                            'enrollment_id',
                                            e.target.value,
                                        )
                                    }
                                >
                                    <option value="">
                                        Seleccione matrícula
                                    </option>
                                    {catalog.enrollments.map((o) => (
                                        <option key={o.value} value={o.value}>
                                            {o.label}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.enrollment_id}
                                    className="mt-1"
                                />
                            </div>

                            <div className="sm:col-span-2">
                                <InputLabel
                                    htmlFor="payment_concept_id"
                                    value="Concepto de pago *"
                                />
                                <select
                                    id="payment_concept_id"
                                    className={inputClass}
                                    value={form.data.payment_concept_id}
                                    onChange={(e) =>
                                        form.setData(
                                            'payment_concept_id',
                                            e.target.value,
                                        )
                                    }
                                >
                                    <option value="">
                                        Seleccione concepto
                                    </option>
                                    {catalog.concepts.map((o) => (
                                        <option key={o.value} value={o.value}>
                                            {o.label}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.payment_concept_id}
                                    className="mt-1"
                                />
                            </div>

                            <div>
                                <InputLabel htmlFor="month" value="Mes *" />
                                <select
                                    id="month"
                                    className={inputClass}
                                    value={form.data.month}
                                    onChange={(e) =>
                                        form.setData('month', e.target.value)
                                    }
                                >
                                    {Array.from(
                                        { length: 12 },
                                        (_, i) => i + 1,
                                    ).map((m) => (
                                        <option key={m} value={String(m)}>
                                            {m}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.month}
                                    className="mt-1"
                                />
                            </div>
                            <div>
                                <InputLabel htmlFor="year" value="Año *" />
                                <TextInput
                                    id="year"
                                    type="number"
                                    min={1990}
                                    max={2100}
                                    className={inputClass}
                                    value={form.data.year}
                                    onChange={(e) =>
                                        form.setData('year', e.target.value)
                                    }
                                />
                                <InputError
                                    message={form.errors.year}
                                    className="mt-1"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    htmlFor="amount"
                                    value="Monto (S/) *"
                                />
                                <TextInput
                                    id="amount"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    className={inputClass}
                                    value={form.data.amount}
                                    onChange={(e) =>
                                        form.setData('amount', e.target.value)
                                    }
                                />
                                <InputError
                                    message={form.errors.amount}
                                    className="mt-1"
                                />
                            </div>
                            <div>
                                <InputLabel
                                    htmlFor="due_date"
                                    value="Vencimiento *"
                                />
                                <TextInput
                                    id="due_date"
                                    type="date"
                                    className={inputClass}
                                    value={form.data.due_date}
                                    onChange={(e) =>
                                        form.setData(
                                            'due_date',
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputError
                                    message={form.errors.due_date}
                                    className="mt-1"
                                />
                            </div>

                            <div className="sm:col-span-2">
                                <InputLabel htmlFor="status" value="Estado *" />
                                <select
                                    id="status"
                                    className={inputClass}
                                    value={form.data.status}
                                    onChange={(e) =>
                                        form.setData('status', e.target.value)
                                    }
                                >
                                    {catalog.statuses.map((s) => (
                                        <option key={s.value} value={s.value}>
                                            {s.label}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.status}
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
                                <InputError
                                    message={form.errors.observations}
                                    className="mt-1"
                                />
                            </div>
                        </div>

                        <div className="flex justify-end gap-3">
                            <PrimaryButton disabled={form.processing}>
                                Actualizar
                            </PrimaryButton>
                        </div>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
