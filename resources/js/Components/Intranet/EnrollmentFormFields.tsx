import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import type {
    EnrollmentFormCatalog,
    EnrollmentFormState,
    EnrollmentStudentPreview,
    SelectOption,
} from '@/types';
import type { InertiaFormProps } from '@inertiajs/react';
import { Link } from '@inertiajs/react';
import { useEffect, useMemo } from 'react';
import { EnrollmentStudentPicker } from '@/Components/Intranet/EnrollmentStudentPicker';
import { useEnrollmentCatalogSync } from '@/Components/Intranet/enrollmentFormHooks';
import { useEnrollmentStudentPicker } from '@/Components/Intranet/useEnrollmentStudentPicker';

const inputClass =
    'mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900';

type Props = {
    form: InertiaFormProps<EnrollmentFormState>;
    catalog: EnrollmentFormCatalog;
    enrollmentCodeOptional?: boolean;
    initialStudentPreview?: EnrollmentStudentPreview | null;
};

export function EnrollmentFormFields({
    form,
    catalog,
    enrollmentCodeOptional = false,
    initialStudentPreview = null,
}: Props) {
    const { data, setData, errors } = form;
    const picker = useEnrollmentStudentPicker(form, initialStudentPreview);

    const guardianOptions: SelectOption[] = picker.preview?.guardians ?? [];

    useEffect(() => {
        const allowed = new Set(guardianOptions.map((g) => g.value));
        if (data.guardian_id && !allowed.has(data.guardian_id)) {
            setData('guardian_id', '');
        }
    }, [data.guardian_id, guardianOptions, setData]);

    useEnrollmentCatalogSync(form, catalog);

    const classroomOptions = useMemo(() => {
        const floating = catalog.classrooms_without_section;
        const bySec = catalog.classrooms_by_section[data.section_id] ?? [];
        return [...floating, ...bySec];
    }, [catalog.classrooms_by_section, catalog.classrooms_without_section, data.section_id]);

    useEffect(() => {
        const valid = new Set(classroomOptions.map((c) => c.value));
        valid.add('');
        if (data.classroom_id && !valid.has(data.classroom_id)) {
            setData('classroom_id', '');
        }
    }, [classroomOptions, data.classroom_id, setData]);

    const gradeOptions = catalog.grades_by_level[data.educational_level_id] ?? [];
    const sectionOptions = catalog.sections_by_grade[data.grade_id] ?? [];
    const selectedStudent =
        picker.preview && !picker.changingStudent ? picker.preview : null;

    return (
        <div className="grid gap-6 sm:grid-cols-2">
            <div className="sm:col-span-2">
                <InputLabel
                    htmlFor="enrollment_code"
                    value={
                        enrollmentCodeOptional
                            ? 'Código de matrícula (opcional)'
                            : 'Código de matrícula *'
                    }
                />
                <TextInput
                    id="enrollment_code"
                    type="text"
                    className={inputClass}
                    value={data.enrollment_code}
                    onChange={(e) => setData('enrollment_code', e.target.value)}
                    placeholder={
                        enrollmentCodeOptional
                            ? 'Vacío = se genera automáticamente (MAT-AÑO-…)'
                            : undefined
                    }
                />
                <InputError message={errors.enrollment_code} className="mt-1" />
            </div>

            <EnrollmentStudentPicker
                studentError={errors.student_id}
                selectedStudent={selectedStudent}
                showSearchUi={picker.changingStudent || !picker.preview}
                searchWrapRef={picker.searchWrapRef}
                searchText={picker.searchText}
                onSearchTextChange={(value) => {
                    picker.setSearchText(value);
                    picker.setSearchOpen(true);
                }}
                onSearchFocus={() => picker.setSearchOpen(true)}
                searchMin={picker.searchMin}
                searchLoading={picker.searchLoading}
                searchError={picker.searchError}
                searchOpen={picker.searchOpen}
                searchHits={picker.searchHits}
                onPickStudent={picker.pickStudent}
                pickLoading={picker.pickLoading}
                onChangeStudent={picker.startChangeStudent}
            />

            <div className="sm:col-span-2">
                <InputLabel htmlFor="guardian_id" value="Apoderado responsable" />
                <select
                    id="guardian_id"
                    className={inputClass}
                    value={data.guardian_id}
                    onChange={(e) => setData('guardian_id', e.target.value)}
                    disabled={
                        picker.pickLoading ||
                        !picker.preview ||
                        picker.changingStudent ||
                        !data.student_id
                    }
                >
                    <option value="">— Sin asignar —</option>
                    {guardianOptions.map((g) => (
                        <option key={g.value} value={g.value}>
                            {g.label}
                        </option>
                    ))}
                </select>
                {selectedStudent && guardianOptions.length === 0 ? (
                    <div
                        className="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950"
                        role="status"
                    >
                        <p className="font-semibold">Sin apoderados vinculados</p>
                        <p className="mt-1 text-amber-900/90">
                            Este estudiante no tiene apoderados registrados en su ficha. Puede
                            continuar la matrícula sin apoderado o vincular apoderados desde{' '}
                            <Link
                                href={route('intranet.students.show', selectedStudent.id)}
                                className="font-semibold underline underline-offset-2"
                            >
                                la ficha del estudiante
                            </Link>
                            .
                        </p>
                    </div>
                ) : (
                    <p className="mt-1 text-xs text-plomo">
                        Solo apoderados vinculados al estudiante en su ficha.
                    </p>
                )}
                <InputError message={errors.guardian_id} className="mt-1" />
            </div>

            <div>
                <InputLabel htmlFor="academic_year_id" value="Año académico *" />
                <select
                    id="academic_year_id"
                    className={inputClass}
                    value={data.academic_year_id}
                    onChange={(e) => setData('academic_year_id', e.target.value)}
                >
                    <option value="">Seleccione…</option>
                    {catalog.academic_years.map((y) => (
                        <option key={y.value} value={y.value}>
                            {y.label}
                        </option>
                    ))}
                </select>
                <InputError message={errors.academic_year_id} className="mt-1" />
            </div>

            <div>
                <InputLabel htmlFor="enrollment_date" value="Fecha de matrícula *" />
                <TextInput
                    id="enrollment_date"
                    type="date"
                    className={inputClass}
                    value={data.enrollment_date}
                    onChange={(e) => setData('enrollment_date', e.target.value)}
                />
                <InputError message={errors.enrollment_date} className="mt-1" />
            </div>

            <div>
                <InputLabel htmlFor="educational_level_id" value="Nivel educativo *" />
                <select
                    id="educational_level_id"
                    className={inputClass}
                    value={data.educational_level_id}
                    onChange={(e) => setData('educational_level_id', e.target.value)}
                >
                    <option value="">Seleccione…</option>
                    {catalog.levels.map((l) => (
                        <option key={l.value} value={l.value}>
                            {l.label}
                        </option>
                    ))}
                </select>
                <InputError message={errors.educational_level_id} className="mt-1" />
            </div>

            <div>
                <InputLabel htmlFor="grade_id" value="Grado *" />
                <select
                    id="grade_id"
                    className={inputClass}
                    value={data.grade_id}
                    onChange={(e) => setData('grade_id', e.target.value)}
                    disabled={!gradeOptions.length}
                >
                    <option value="">Seleccione…</option>
                    {gradeOptions.map((g) => (
                        <option key={g.value} value={g.value}>
                            {g.label}
                        </option>
                    ))}
                </select>
                <InputError message={errors.grade_id} className="mt-1" />
            </div>

            <div>
                <InputLabel htmlFor="section_id" value="Sección *" />
                <select
                    id="section_id"
                    className={inputClass}
                    value={data.section_id}
                    onChange={(e) => setData('section_id', e.target.value)}
                    disabled={!sectionOptions.length}
                >
                    <option value="">Seleccione…</option>
                    {sectionOptions.map((s) => (
                        <option key={s.value} value={s.value}>
                            {s.label}
                        </option>
                    ))}
                </select>
                <InputError message={errors.section_id} className="mt-1" />
            </div>

            <div>
                <InputLabel htmlFor="classroom_id" value="Aula" />
                <select
                    id="classroom_id"
                    className={inputClass}
                    value={data.classroom_id}
                    onChange={(e) => setData('classroom_id', e.target.value)}
                >
                    <option value="">— Sin aula —</option>
                    {classroomOptions.map((c) => (
                        <option key={c.value} value={c.value}>
                            {c.label}
                        </option>
                    ))}
                </select>
                <InputError message={errors.classroom_id} className="mt-1" />
            </div>

            <div>
                <InputLabel htmlFor="amount" value="Monto (S/) *" />
                <TextInput
                    id="amount"
                    type="number"
                    min="0"
                    step="0.01"
                    className={inputClass}
                    value={data.amount}
                    onChange={(e) => setData('amount', e.target.value)}
                />
                <InputError message={errors.amount} className="mt-1" />
            </div>

            <div>
                <InputLabel htmlFor="status" value="Estado *" />
                <select
                    id="status"
                    className={inputClass}
                    value={data.status}
                    onChange={(e) => setData('status', e.target.value)}
                >
                    {catalog.statuses.map((s) => (
                        <option key={s.value} value={s.value}>
                            {s.label}
                        </option>
                    ))}
                </select>
                <InputError message={errors.status} className="mt-1" />
            </div>

            <div className="sm:col-span-2">
                <InputLabel htmlFor="observations" value="Observaciones" />
                <textarea
                    id="observations"
                    rows={4}
                    className={inputClass}
                    value={data.observations}
                    onChange={(e) => setData('observations', e.target.value)}
                />
                <InputError message={errors.observations} className="mt-1" />
            </div>
        </div>
    );
}
