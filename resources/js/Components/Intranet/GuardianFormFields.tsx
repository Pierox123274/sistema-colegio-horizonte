import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import type { GuardianFormCatalog, GuardianFullFormState } from '@/types';
import type { InertiaFormProps } from '@inertiajs/react';

type Props = {
    form: InertiaFormProps<GuardianFullFormState>;
    catalog: GuardianFormCatalog;
};

const inputClass =
    'mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900';

export function GuardianFormFields({ form, catalog }: Props) {
    const { data, setData, errors } = form;

    return (
        <div className="grid gap-6 sm:grid-cols-2">
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
                <InputLabel htmlFor="document_number" value="Número de documento" />
                <TextInput
                    id="document_number"
                    value={data.document_number}
                    className={inputClass}
                    onChange={(e) =>
                        setData('document_number', e.target.value)
                    }
                />
                <InputError message={errors.document_number} className="mt-2" />
            </div>

            <div>
                <InputLabel
                    htmlFor="relationship_type"
                    value="Parentesco principal *"
                />
                <select
                    id="relationship_type"
                    className={inputClass}
                    value={data.relationship_type}
                    onChange={(e) =>
                        setData('relationship_type', e.target.value)
                    }
                >
                    {catalog.relationship_types.map((o) => (
                        <option key={o.value} value={o.value}>
                            {o.label}
                        </option>
                    ))}
                </select>
                <InputError message={errors.relationship_type} className="mt-2" />
            </div>

            <div>
                <InputLabel htmlFor="phone" value="Teléfono principal *" />
                <TextInput
                    id="phone"
                    value={data.phone}
                    className={inputClass}
                    onChange={(e) => setData('phone', e.target.value)}
                />
                <InputError message={errors.phone} className="mt-2" />
            </div>

            <div>
                <InputLabel htmlFor="secondary_phone" value="Teléfono secundario" />
                <TextInput
                    id="secondary_phone"
                    value={data.secondary_phone}
                    className={inputClass}
                    onChange={(e) =>
                        setData('secondary_phone', e.target.value)
                    }
                />
                <InputError message={errors.secondary_phone} className="mt-2" />
            </div>

            <div className="sm:col-span-2">
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
                <InputLabel htmlFor="occupation" value="Ocupación" />
                <TextInput
                    id="occupation"
                    value={data.occupation}
                    className={inputClass}
                    onChange={(e) => setData('occupation', e.target.value)}
                />
                <InputError message={errors.occupation} className="mt-2" />
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

            <div className="sm:col-span-2">
                <InputLabel htmlFor="workplace" value="Centro de trabajo" />
                <TextInput
                    id="workplace"
                    value={data.workplace}
                    className={inputClass}
                    onChange={(e) => setData('workplace', e.target.value)}
                />
                <InputError message={errors.workplace} className="mt-2" />
            </div>

            <div className="sm:col-span-2 flex items-start gap-3 rounded-lg border border-plomo/15 bg-navy-50/50 px-4 py-3">
                <input
                    id="is_emergency_contact"
                    type="checkbox"
                    checked={data.is_emergency_contact}
                    onChange={(e) =>
                        setData('is_emergency_contact', e.target.checked)
                    }
                    className="mt-1 h-4 w-4 rounded border-plomo text-navy-900 focus:ring-navy-900"
                />
                <div>
                    <label
                        htmlFor="is_emergency_contact"
                        className="text-sm font-medium text-navy-900"
                    >
                        Contacto de emergencia institucional
                    </label>
                    <p className="text-xs text-plomo">
                        Marque si este apoderado debe figurar como contacto prioritario ante emergencias.
                    </p>
                </div>
            </div>
        </div>
    );
}
