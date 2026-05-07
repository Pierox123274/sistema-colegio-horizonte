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

type P = PageProps<{
    catalog: {
        enrollments: SelectOption[];
        concepts: ConceptOpt[];
        statuses: SelectOption[];
    };
}>;

export default function PensionsCreate() {
    const { catalog } = usePage<P>().props;
    const pendiente =
        catalog.statuses.find((s) => s.value === 'pendiente')?.value ??
        catalog.statuses[0]?.value ??
        'pendiente';

    const form = useForm({
        enrollment_id: catalog.enrollments[0]?.value ?? '',
        payment_concept_id: catalog.concepts[0]?.value ?? '',
        month: String(new Date().getMonth() + 1),
        year: String(new Date().getFullYear()),
        amount: '0',
        due_date: new Date().toISOString().slice(0, 10),
        status: pendiente,
        observations: '',
    });

    const applyDefaultAmount = (conceptId: string) => {
        const c = catalog.concepts.find((x) => x.value === conceptId);
        if (c?.default_amount != null && c.default_amount !== '') {
            form.setData('amount', c.default_amount);
        }
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.post(route('intranet.pensions.store'));
    };

    return (
        <IntranetLayout title="Nueva pensión">
            <Head title="Nueva pensión — Horizonte" />

            <PageContainer>
                <SectionTitle
                    title="Nueva pensión"
                    description="Una matrícula no puede duplicar periodo (mes y año)."
                    actions={
                        <Link
                            href={route('intranet.pensions.index')}
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
                                    onChange={(e) => {
                                        const v = e.target.value;
                                        form.setData(
                                            'payment_concept_id',
                                            v,
                                        );
                                        applyDefaultAmount(v);
                                    }}
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
                                Guardar
                            </PrimaryButton>
                        </div>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
