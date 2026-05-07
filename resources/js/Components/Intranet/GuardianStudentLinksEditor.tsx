import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import type {
    GuardianFormCatalog,
    GuardianFullFormState,
    GuardianStudentLinkDraft,
    StudentOption,
} from '@/types';
import type { InertiaFormProps } from '@inertiajs/react';
import { Plus, Trash2 } from 'lucide-react';

export type { GuardianStudentLinkDraft, GuardianFullFormState };

type Props = {
    form: InertiaFormProps<GuardianFullFormState>;
    catalog: GuardianFormCatalog;
    studentOptions: StudentOption[];
};

const rowClass =
    'rounded-lg border border-plomo/15 bg-white px-4 py-4 shadow-sm';

export function GuardianStudentLinksEditor({
    form,
    catalog,
    studentOptions,
}: Props) {
    const { data, setData, errors } = form;

    const addRow = () => {
        const defaultRel = catalog.relationship_types[0]?.value ?? 'padre';
        setData('students', [
            ...data.students,
            {
                student_id: '',
                relationship: defaultRel,
                is_primary: false,
                is_financial_responsible: false,
                emergency_priority: '',
                observations: '',
            },
        ]);
    };

    const removeRow = (index: number) => {
        const next = data.students.filter((_, i) => i !== index);
        setData('students', next);
    };

    const updateRow = (
        index: number,
        patch: Partial<GuardianStudentLinkDraft>,
    ) => {
        const next = data.students.map((row, i) =>
            i === index ? { ...row, ...patch } : row,
        );
        setData('students', next);
    };

    const selectedIds = new Set(
        data.students
            .map((r) => r.student_id)
            .filter((id): id is number => typeof id === 'number' && id > 0),
    );

    return (
        <div className="space-y-4">
            <div className="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h3 className="text-sm font-bold uppercase tracking-wider text-navy-900">
                        Estudiantes vinculados
                    </h3>
                    <p className="mt-1 text-xs text-plomo">
                        Relacione estudiantes existentes. Puede marcar responsable
                        económico y prioridad de contacto por estudiante.
                    </p>
                </div>
                <button
                    type="button"
                    onClick={addRow}
                    className="inline-flex items-center gap-2 rounded-lg border border-plomo/20 bg-white px-3 py-2 text-sm font-semibold text-navy-900 shadow-sm hover:bg-navy-50"
                >
                    <Plus className="h-4 w-4" />
                    Añadir vínculo
                </button>
            </div>

            {data.students.length === 0 ? (
                <p className="rounded-lg border border-dashed border-plomo/25 bg-navy-50/30 px-4 py-6 text-center text-sm text-plomo">
                    Sin vínculos. Use «Añadir vínculo» para asociar estudiantes.
                </p>
            ) : null}

            <div className="space-y-4">
                {data.students.map((row, index) => (
                    <div key={index} className={rowClass}>
                        <div className="mb-3 flex items-start justify-between gap-2">
                            <span className="text-xs font-semibold uppercase tracking-wide text-plomo">
                                Vínculo {index + 1}
                            </span>
                            <button
                                type="button"
                                onClick={() => removeRow(index)}
                                className="rounded-md p-1 text-plomo hover:bg-red-50 hover:text-brand-red"
                                aria-label="Quitar vínculo"
                            >
                                <Trash2 className="h-4 w-4" />
                            </button>
                        </div>

                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="sm:col-span-2">
                                <InputLabel
                                    value="Estudiante *"
                                    htmlFor={`student_${index}`}
                                />
                                <select
                                    id={`student_${index}`}
                                    className="mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900"
                                    value={row.student_id === '' ? '' : row.student_id}
                                    onChange={(e) => {
                                        const v = e.target.value;
                                        updateRow(index, {
                                            student_id:
                                                v === '' ? '' : Number(v),
                                        });
                                    }}
                                >
                                    <option value="">Seleccione…</option>
                                    {studentOptions.map((opt) => {
                                        const takenElsewhere =
                                            selectedIds.has(opt.value) &&
                                            opt.value !== row.student_id;
                                        return (
                                            <option
                                                key={opt.value}
                                                value={opt.value}
                                                disabled={takenElsewhere}
                                            >
                                                {opt.label}
                                                {takenElsewhere
                                                    ? ' (ya asignado)'
                                                    : ''}
                                            </option>
                                        );
                                    })}
                                </select>
                                <InputError
                                    message={errors[`students.${index}.student_id`]}
                                    className="mt-2"
                                />
                            </div>

                            <div>
                                <InputLabel value="Parentesco con el estudiante *" />
                                <select
                                    className="mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900"
                                    value={row.relationship}
                                    onChange={(e) =>
                                        updateRow(index, {
                                            relationship: e.target.value,
                                        })
                                    }
                                >
                                    {catalog.relationship_types.map((o) => (
                                        <option key={o.value} value={o.value}>
                                            {o.label}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={errors[`students.${index}.relationship`]}
                                    className="mt-2"
                                />
                            </div>

                            <div>
                                <InputLabel value="Prioridad emergencia (1 = mayor)" />
                                <input
                                    type="number"
                                    min={1}
                                    max={99}
                                    placeholder="—"
                                    className="mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900"
                                    value={row.emergency_priority === '' ? '' : row.emergency_priority}
                                    onChange={(e) => {
                                        const v = e.target.value;
                                        updateRow(index, {
                                            emergency_priority:
                                                v === '' ? '' : Number(v),
                                        });
                                    }}
                                />
                                <InputError
                                    message={errors[`students.${index}.emergency_priority`]}
                                    className="mt-2"
                                />
                            </div>

                            <div className="flex flex-col gap-3 sm:col-span-2 sm:flex-row sm:items-center">
                                <label className="flex cursor-pointer items-center gap-2 text-sm text-navy-900">
                                    <input
                                        type="checkbox"
                                        checked={row.is_primary}
                                        onChange={(e) =>
                                            updateRow(index, {
                                                is_primary: e.target.checked,
                                            })
                                        }
                                        className="h-4 w-4 rounded border-plomo text-navy-900 focus:ring-navy-900"
                                    />
                                    Contacto principal del estudiante
                                </label>
                                <label className="flex cursor-pointer items-center gap-2 text-sm text-navy-900">
                                    <input
                                        type="checkbox"
                                        checked={row.is_financial_responsible}
                                        onChange={(e) =>
                                            updateRow(index, {
                                                is_financial_responsible:
                                                    e.target.checked,
                                            })
                                        }
                                        className="h-4 w-4 rounded border-plomo text-navy-900 focus:ring-navy-900"
                                    />
                                    Responsable económico
                                </label>
                            </div>

                            <div className="sm:col-span-2">
                                <InputLabel value="Observaciones del vínculo" />
                                <textarea
                                    rows={2}
                                    className="mt-1 block w-full resize-y rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900"
                                    value={row.observations}
                                    onChange={(e) =>
                                        updateRow(index, {
                                            observations: e.target.value,
                                        })
                                    }
                                />
                                <InputError
                                    message={errors[`students.${index}.observations`]}
                                    className="mt-2"
                                />
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}
