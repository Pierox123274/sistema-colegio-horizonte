import { Card } from '@/Components/Intranet/Card';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import PrimaryButton from '@/Components/PrimaryButton';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import type { FormEventHandler } from 'react';

type YearRow = {
    id: number;
    name: string;
    year: number;
    starts_at: string;
    ends_at: string;
    is_active: boolean;
};

type AcademicYearFormState = {
    name: string;
    year: string;
    starts_at: string;
    ends_at: string;
    is_active: boolean;
};

type EditPageProps = PageProps<{
    year: YearRow;
}>;

const inputClass =
    'mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900';

function toState(y: YearRow): AcademicYearFormState {
    return {
        name: y.name,
        year: String(y.year),
        starts_at: y.starts_at?.slice(0, 10) ?? '',
        ends_at: y.ends_at?.slice(0, 10) ?? '',
        is_active: y.is_active,
    };
}

export default function AcademicYearsEdit() {
    const { year } = usePage<EditPageProps>().props;

    const form = useForm<AcademicYearFormState>(toState(year));

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.put(route('intranet.academic-years.update', year.id));
    };

    return (
        <IntranetLayout title="Editar año académico">
            <Head title={`${year.name} — Año académico — Horizonte`} />

            <PageContainer>
                <SectionTitle
                    title="Editar año académico"
                    description={`Año calendario ${year.year}`}
                    actions={
                        <Link
                            href={route('intranet.academic-years.index')}
                            className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                        >
                            Volver al listado
                        </Link>
                    }
                />

                <Card>
                    <form onSubmit={submit} className="space-y-6">
                        <div className="grid gap-6 sm:grid-cols-2">
                            <div className="sm:col-span-2">
                                <InputLabel htmlFor="name" value="Nombre *" />
                                <TextInput
                                    id="name"
                                    type="text"
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
                            <div className="flex items-end pb-1">
                                <label className="flex cursor-pointer items-center gap-2 text-sm font-medium text-navy-900">
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
                                    Año activo (vigente)
                                </label>
                            </div>
                            <div>
                                <InputLabel
                                    htmlFor="starts_at"
                                    value="Inicio *"
                                />
                                <TextInput
                                    id="starts_at"
                                    type="date"
                                    className={inputClass}
                                    value={form.data.starts_at}
                                    onChange={(e) =>
                                        form.setData(
                                            'starts_at',
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputError
                                    message={form.errors.starts_at}
                                    className="mt-1"
                                />
                            </div>
                            <div>
                                <InputLabel htmlFor="ends_at" value="Fin *" />
                                <TextInput
                                    id="ends_at"
                                    type="date"
                                    className={inputClass}
                                    value={form.data.ends_at}
                                    onChange={(e) =>
                                        form.setData('ends_at', e.target.value)
                                    }
                                />
                                <InputError
                                    message={form.errors.ends_at}
                                    className="mt-1"
                                />
                            </div>
                        </div>

                        <div className="flex flex-wrap items-center gap-3 border-t border-plomo/10 pt-6">
                            <PrimaryButton disabled={form.processing}>
                                Guardar cambios
                            </PrimaryButton>
                            <Link
                                href={route('intranet.academic-years.index')}
                                className="text-sm font-medium text-plomo hover:text-navy-900"
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
