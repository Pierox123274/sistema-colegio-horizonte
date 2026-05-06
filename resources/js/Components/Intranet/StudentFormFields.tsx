import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import type { StudentFormCatalog } from '@/types';
import type { InertiaFormProps } from '@inertiajs/react';
import { useEffect } from 'react';

export type StudentFormState = {
    code: string;
    first_name: string;
    last_name: string;
    document_type: string;
    document_number: string;
    birth_date: string;
    gender: string;
    educational_level: string;
    grade: string;
    section: string;
    status: string;
    address: string;
    phone: string;
    email: string;
    medical_observations: string;
};

type Props = {
    form: InertiaFormProps<StudentFormState>;
    catalog: StudentFormCatalog;
};

const inputClass =
    'mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900';

export function StudentFormFields({ form, catalog }: Props) {
    const { data, setData, errors } = form;

    useEffect(() => {
        const grades =
            catalog.grades_by_level[data.educational_level] ?? [];
        if (grades.length && !grades.includes(data.grade)) {
            setData('grade', grades[0]);
        }
    }, [data.educational_level, catalog.grades_by_level]);

    return (
        <div className="grid gap-6 sm:grid-cols-2">
            <div className="sm:col-span-2">
                <InputLabel htmlFor="code" value="Código institucional *" />
                <TextInput
                    id="code"
                    value={data.code}
                    className={inputClass}
                    onChange={(e) => setData('code', e.target.value)}
                    autoComplete="off"
                />
                <InputError message={errors.code} className="mt-2" />
            </div>

            <div>
                <InputLabel htmlFor="first_name" value="Nombres *" />
                <TextInput
                    id="first_name"
                    value={data.first_name}
                    className={inputClass}
                    onChange={(e) => setData('first_name', e.target.value)}
                />
                <InputError message={errors.first_name} className="mt-2" />
            </div>

            <div>
                <InputLabel htmlFor="last_name" value="Apellidos *" />
                <TextInput
                    id="last_name"
                    value={data.last_name}
                    className={inputClass}
                    onChange={(e) => setData('last_name', e.target.value)}
                />
                <InputError message={errors.last_name} className="mt-2" />
            </div>

            <div>
                <InputLabel htmlFor="document_type" value="Tipo de documento *" />
                <select
                    id="document_type"
                    className={inputClass}
                    value={data.document_type}
                    onChange={(e) =>
                        setData('document_type', e.target.value)
                    }
                >
                    {catalog.document_types.map((o) => (
                        <option key={o.value} value={o.value}>
                            {o.label}
                        </option>
                    ))}
                </select>
                <InputError message={errors.document_type} className="mt-2" />
            </div>

            <div>
                <InputLabel
                    htmlFor="document_number"
                    value="Número de documento"
                />
                <TextInput
                    id="document_number"
                    value={data.document_number}
                    className={inputClass}
                    onChange={(e) =>
                        setData('document_number', e.target.value)
                    }
                />
                <InputError
                    message={errors.document_number}
                    className="mt-2"
                />
            </div>

            <div>
                <InputLabel htmlFor="birth_date" value="Fecha de nacimiento *" />
                <TextInput
                    id="birth_date"
                    type="date"
                    value={data.birth_date}
                    className={inputClass}
                    onChange={(e) => setData('birth_date', e.target.value)}
                />
                <InputError message={errors.birth_date} className="mt-2" />
            </div>

            <div>
                <InputLabel htmlFor="gender" value="Género *" />
                <select
                    id="gender"
                    className={inputClass}
                    value={data.gender}
                    onChange={(e) => setData('gender', e.target.value)}
                >
                    {catalog.genders.map((o) => (
                        <option key={o.value} value={o.value}>
                            {o.label}
                        </option>
                    ))}
                </select>
                <InputError message={errors.gender} className="mt-2" />
            </div>

            <div>
                <InputLabel
                    htmlFor="educational_level"
                    value="Nivel educativo *"
                />
                <select
                    id="educational_level"
                    className={inputClass}
                    value={data.educational_level}
                    onChange={(e) =>
                        setData('educational_level', e.target.value)
                    }
                >
                    {catalog.educational_levels.map((o) => (
                        <option key={o.value} value={o.value}>
                            {o.label}
                        </option>
                    ))}
                </select>
                <InputError
                    message={errors.educational_level}
                    className="mt-2"
                />
            </div>

            <div>
                <InputLabel htmlFor="grade" value="Grado *" />
                <select
                    id="grade"
                    className={inputClass}
                    value={data.grade}
                    onChange={(e) => setData('grade', e.target.value)}
                >
                    {(catalog.grades_by_level[data.educational_level] ?? []).map(
                        (g) => (
                            <option key={g} value={g}>
                                {g}
                            </option>
                        ),
                    )}
                </select>
                <InputError message={errors.grade} className="mt-2" />
            </div>

            <div>
                <InputLabel htmlFor="section" value="Sección" />
                <TextInput
                    id="section"
                    value={data.section}
                    className={inputClass}
                    onChange={(e) => setData('section', e.target.value)}
                    placeholder="Ej. A"
                />
                <InputError message={errors.section} className="mt-2" />
            </div>

            <div>
                <InputLabel htmlFor="status" value="Estado *" />
                <select
                    id="status"
                    className={inputClass}
                    value={data.status}
                    onChange={(e) => setData('status', e.target.value)}
                >
                    {catalog.statuses.map((o) => (
                        <option key={o.value} value={o.value}>
                            {o.label}
                        </option>
                    ))}
                </select>
                <InputError message={errors.status} className="mt-2" />
            </div>

            <div className="sm:col-span-2">
                <InputLabel htmlFor="address" value="Dirección" />
                <TextInput
                    id="address"
                    value={data.address}
                    className={inputClass}
                    onChange={(e) => setData('address', e.target.value)}
                />
                <InputError message={errors.address} className="mt-2" />
            </div>

            <div>
                <InputLabel htmlFor="phone" value="Teléfono" />
                <TextInput
                    id="phone"
                    value={data.phone}
                    className={inputClass}
                    onChange={(e) => setData('phone', e.target.value)}
                />
                <InputError message={errors.phone} className="mt-2" />
            </div>

            <div>
                <InputLabel htmlFor="email" value="Correo electrónico" />
                <TextInput
                    id="email"
                    type="email"
                    value={data.email}
                    className={inputClass}
                    onChange={(e) => setData('email', e.target.value)}
                />
                <InputError message={errors.email} className="mt-2" />
            </div>

            <div className="sm:col-span-2">
                <InputLabel
                    htmlFor="medical_observations"
                    value="Observaciones médicas / alergias"
                />
                <textarea
                    id="medical_observations"
                    rows={3}
                    className={`${inputClass} resize-y`}
                    value={data.medical_observations}
                    onChange={(e) =>
                        setData('medical_observations', e.target.value)
                    }
                />
                <InputError
                    message={errors.medical_observations}
                    className="mt-2"
                />
            </div>
        </div>
    );
}
