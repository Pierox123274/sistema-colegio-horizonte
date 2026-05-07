import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import { DOCUMENT_TYPE_LABELS } from '@/lib/studentLabels';
import type {
    EnrollmentFormCatalog,
    EnrollmentFormState,
    EnrollmentStudentPreview,
    EnrollmentStudentSearchHit,
    SelectOption,
} from '@/types';
import type { InertiaFormProps } from '@inertiajs/react';
import { Link } from '@inertiajs/react';
import axios from 'axios';
import { Loader2, Search, User } from 'lucide-react';
import {
    useCallback,
    useEffect,
    useMemo,
    useRef,
    useState,
} from 'react';

const inputClass =
    'mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900';

const SEARCH_MIN = 2;
const DEBOUNCE_MS = 320;

type Props = {
    form: InertiaFormProps<EnrollmentFormState>;
    catalog: EnrollmentFormCatalog;
    /** En alta el código puede omitirse (generación automática). */
    enrollmentCodeOptional?: boolean;
    /** Estudiante seleccionado al cargar edición (vista previa + apoderados). */
    initialStudentPreview?: EnrollmentStudentPreview | null;
};

export function EnrollmentFormFields({
    form,
    catalog,
    enrollmentCodeOptional = false,
    initialStudentPreview = null,
}: Props) {
    const { data, setData, errors } = form;

    const [preview, setPreview] = useState<EnrollmentStudentPreview | null>(
        initialStudentPreview,
    );
    const [changingStudent, setChangingStudent] = useState(false);
    const [searchText, setSearchText] = useState('');
    const [searchHits, setSearchHits] = useState<EnrollmentStudentSearchHit[]>(
        [],
    );
    const [searchLoading, setSearchLoading] = useState(false);
    const [searchOpen, setSearchOpen] = useState(false);
    const [searchError, setSearchError] = useState<string | null>(null);
    const [pickLoading, setPickLoading] = useState(false);

    const searchWrapRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        setPreview(initialStudentPreview);
        if (initialStudentPreview) {
            setChangingStudent(false);
        }
    }, [initialStudentPreview]);

    const guardianOptions: SelectOption[] = preview?.guardians ?? [];

    useEffect(() => {
        const allowed = new Set(guardianOptions.map((g) => g.value));
        if (data.guardian_id && !allowed.has(data.guardian_id)) {
            setData('guardian_id', '');
        }
    }, [data.guardian_id, guardianOptions, setData]);

    useEffect(() => {
        const grades =
            catalog.grades_by_level[data.educational_level_id] ?? [];
        if (!data.educational_level_id) {
            return;
        }
        if (!grades.length) {
            if (data.grade_id) {
                setData('grade_id', '');
            }
            if (data.section_id) {
                setData('section_id', '');
            }
            if (data.classroom_id) {
                setData('classroom_id', '');
            }
            return;
        }
        if (!grades.some((g) => g.value === data.grade_id)) {
            setData('grade_id', grades[0].value);
        }
    }, [data.educational_level_id, catalog.grades_by_level]);

    useEffect(() => {
        const sections = catalog.sections_by_grade[data.grade_id] ?? [];
        if (!data.grade_id) {
            return;
        }
        if (!sections.length) {
            if (data.section_id) {
                setData('section_id', '');
            }
            if (data.classroom_id) {
                setData('classroom_id', '');
            }
            return;
        }
        if (!sections.some((s) => s.value === data.section_id)) {
            setData('section_id', sections[0].value);
        }
    }, [data.grade_id, catalog.sections_by_grade]);

    const classroomOptions = useMemo(() => {
        const floating = catalog.classrooms_without_section;
        const bySec = catalog.classrooms_by_section[data.section_id] ?? [];
        return [...floating, ...bySec];
    }, [
        catalog.classrooms_by_section,
        catalog.classrooms_without_section,
        data.section_id,
    ]);

    useEffect(() => {
        const valid = new Set(classroomOptions.map((c) => c.value));
        valid.add('');
        if (data.classroom_id && !valid.has(data.classroom_id)) {
            setData('classroom_id', '');
        }
    }, [classroomOptions, data.classroom_id, setData]);

    useEffect(() => {
        const q = searchText.trim();
        if (q.length < SEARCH_MIN) {
            setSearchHits([]);
            setSearchLoading(false);
            setSearchError(null);
            return;
        }

        setSearchLoading(true);
        setSearchError(null);
        const id = window.setTimeout(() => {
            axios
                .get<{ students: EnrollmentStudentSearchHit[] }>(
                    route('intranet.enrollments.students.search'),
                    { params: { q } },
                )
                .then((res) => {
                    setSearchHits(res.data.students ?? []);
                })
                .catch(() => {
                    setSearchError('No se pudo cargar la búsqueda.');
                    setSearchHits([]);
                })
                .finally(() => {
                    setSearchLoading(false);
                });
        }, DEBOUNCE_MS);

        return () => clearTimeout(id);
    }, [searchText]);

    useEffect(() => {
        const onDocDown = (e: MouseEvent) => {
            const el = searchWrapRef.current;
            if (!el || !searchOpen) {
                return;
            }
            if (e.target instanceof Node && !el.contains(e.target)) {
                setSearchOpen(false);
            }
        };
        document.addEventListener('mousedown', onDocDown);
        return () => document.removeEventListener('mousedown', onDocDown);
    }, [searchOpen]);

    const pickStudent = useCallback(
        async (hit: EnrollmentStudentSearchHit) => {
            setPickLoading(true);
            setSearchOpen(false);
            try {
                const res = await axios.get<{ preview: EnrollmentStudentPreview }>(
                    route(
                        'intranet.enrollments.students.preview',
                        hit.id,
                    ),
                );
                const p = res.data.preview;
                setPreview(p);
                setData('student_id', String(p.id));
                setData('guardian_id', '');
                setChangingStudent(false);
                setSearchText('');
                setSearchHits([]);
            } catch {
                setSearchError('No se pudo cargar el estudiante seleccionado.');
            } finally {
                setPickLoading(false);
            }
        },
        [setData],
    );

    const startChangeStudent = () => {
        setChangingStudent(true);
        setPreview(null);
        setData('student_id', '');
        setData('guardian_id', '');
        setSearchText('');
        setSearchHits([]);
        setSearchOpen(false);
    };

    const gradeOptions =
        catalog.grades_by_level[data.educational_level_id] ?? [];
    const sectionOptions = catalog.sections_by_grade[data.grade_id] ?? [];

    const showSearchUi = changingStudent || !preview;
    const selectedStudentCard =
        preview && !changingStudent ? preview : null;

    const docLabel =
        selectedStudentCard?.document_type != null &&
        selectedStudentCard.document_type !== ''
            ? DOCUMENT_TYPE_LABELS[selectedStudentCard.document_type] ??
              selectedStudentCard.document_type
            : null;

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
                    onChange={(e) =>
                        setData('enrollment_code', e.target.value)
                    }
                    placeholder={
                        enrollmentCodeOptional
                            ? 'Vacío = se genera automáticamente (MAT-AÑO-…)'
                            : undefined
                    }
                />
                <InputError
                    message={errors.enrollment_code}
                    className="mt-1"
                />
            </div>

            <div className="sm:col-span-2">
                <InputLabel value="Estudiante *" />

                {selectedStudentCard ? (
                    <div className="mt-2 space-y-3">
                        <div className="rounded-xl border border-plomo/15 bg-gradient-to-br from-navy-50/90 to-white p-4 shadow-sm ring-1 ring-plomo/10">
                            <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between sm:gap-6">
                                <div className="flex min-w-0 flex-1 gap-3">
                                    <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-navy-900 text-white">
                                        <User className="h-5 w-5" aria-hidden />
                                    </div>
                                    <div className="min-w-0">
                                        <p className="font-semibold text-navy-900">
                                            {selectedStudentCard.first_name}{' '}
                                            {selectedStudentCard.last_name}
                                        </p>
                                        <p className="font-mono text-xs text-plomo">
                                            {selectedStudentCard.code}
                                        </p>
                                        <p className="mt-1 text-sm text-navy-800">
                                            {docLabel ? `${docLabel}: ` : ''}
                                            {selectedStudentCard.document_number ??
                                                '—'}
                                        </p>
                                    </div>
                                </div>
                                <div className="flex w-full shrink-0 flex-col gap-2 sm:w-auto sm:flex-row sm:items-center sm:justify-end sm:gap-2">
                                    <Link
                                        href={route(
                                            'intranet.students.show',
                                            selectedStudentCard.id,
                                        )}
                                        className="inline-flex w-full items-center justify-center rounded-lg border border-plomo/25 bg-white px-3 py-2 text-center text-xs font-semibold text-navy-900 shadow-sm ring-1 ring-plomo/10 transition hover:border-plomo/35 hover:bg-navy-50 sm:w-auto sm:min-w-[7.5rem]"
                                    >
                                        Ver ficha
                                    </Link>
                                    <button
                                        type="button"
                                        onClick={startChangeStudent}
                                        className="inline-flex w-full items-center justify-center rounded-lg border border-navy-900/15 bg-navy-900 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-navy-950 sm:w-auto sm:min-w-[10rem]"
                                    >
                                        Cambiar estudiante
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                ) : null}

                {showSearchUi ? (
                    <div ref={searchWrapRef} className="relative mt-2">
                        <div className="relative">
                            <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-plomo" />
                            <input
                                id="student_search"
                                type="search"
                                autoComplete="off"
                                placeholder={`Buscar por código, nombre, apellidos o documento (mín. ${SEARCH_MIN} caracteres)`}
                                className={`${inputClass} pl-10 pr-10`}
                                value={searchText}
                                onChange={(e) => {
                                    setSearchText(e.target.value);
                                    setSearchOpen(true);
                                }}
                                onFocus={() => setSearchOpen(true)}
                                aria-expanded={searchOpen}
                                aria-controls="student-search-results"
                                aria-autocomplete="list"
                            />
                            {searchLoading ? (
                                <Loader2 className="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 animate-spin text-plomo" />
                            ) : null}
                        </div>
                        <p className="mt-1 text-xs text-plomo">
                            Escriba al menos {SEARCH_MIN} caracteres para ver
                            coincidencias (máx. 20 resultados).
                        </p>

                        {searchError ? (
                            <p className="mt-2 text-sm text-rose-700">
                                {searchError}
                            </p>
                        ) : null}

                        {searchOpen &&
                        searchText.trim().length >= SEARCH_MIN &&
                        !searchLoading ? (
                            <div
                                id="student-search-results"
                                role="listbox"
                                className="absolute z-40 mt-1 max-h-60 w-full overflow-auto rounded-lg border border-plomo/20 bg-white py-1 shadow-lg ring-1 ring-black/5"
                            >
                                {searchHits.length === 0 ? (
                                    <div className="px-3 py-4 text-center text-sm text-plomo">
                                        Sin coincidencias.
                                    </div>
                                ) : (
                                    searchHits.map((hit) => (
                                        <button
                                            key={hit.id}
                                            type="button"
                                            role="option"
                                            className="flex w-full flex-col gap-0.5 px-3 py-2.5 text-left text-sm transition hover:bg-navy-50"
                                            onMouseDown={(e) =>
                                                e.preventDefault()
                                            }
                                            onClick={() => pickStudent(hit)}
                                        >
                                            <span className="font-medium text-navy-900">
                                                {hit.first_name}{' '}
                                                {hit.last_name}
                                            </span>
                                            <span className="font-mono text-xs text-plomo">
                                                {hit.code}
                                                {hit.document_number
                                                    ? ` · ${hit.document_number}`
                                                    : ''}
                                            </span>
                                        </button>
                                    ))
                                )}
                            </div>
                        ) : null}
                    </div>
                ) : null}

                {pickLoading ? (
                    <p className="mt-2 flex items-center gap-2 text-sm text-plomo">
                        <Loader2 className="h-4 w-4 animate-spin" aria-hidden />
                        Cargando datos del estudiante…
                    </p>
                ) : null}

                <InputError message={errors.student_id} className="mt-1" />
            </div>

            <div className="sm:col-span-2">
                <InputLabel htmlFor="guardian_id" value="Apoderado responsable" />
                <select
                    id="guardian_id"
                    className={inputClass}
                    value={data.guardian_id}
                    onChange={(e) =>
                        setData('guardian_id', e.target.value)
                    }
                    disabled={
                        pickLoading ||
                        !preview ||
                        changingStudent ||
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
                {selectedStudentCard &&
                guardianOptions.length === 0 ? (
                    <div
                        className="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950"
                        role="status"
                    >
                        <p className="font-semibold">
                            Sin apoderados vinculados
                        </p>
                        <p className="mt-1 text-amber-900/90">
                            Este estudiante no tiene apoderados registrados en su
                            ficha. Puede continuar la matrícula sin apoderado o
                            vincular apoderados desde{' '}
                            <Link
                                href={route(
                                    'intranet.students.show',
                                    selectedStudentCard.id,
                                )}
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
                <InputLabel
                    htmlFor="academic_year_id"
                    value="Año académico *"
                />
                <select
                    id="academic_year_id"
                    className={inputClass}
                    value={data.academic_year_id}
                    onChange={(e) =>
                        setData('academic_year_id', e.target.value)
                    }
                >
                    <option value="">Seleccione…</option>
                    {catalog.academic_years.map((y) => (
                        <option key={y.value} value={y.value}>
                            {y.label}
                        </option>
                    ))}
                </select>
                <InputError
                    message={errors.academic_year_id}
                    className="mt-1"
                />
            </div>

            <div>
                <InputLabel
                    htmlFor="enrollment_date"
                    value="Fecha de matrícula *"
                />
                <TextInput
                    id="enrollment_date"
                    type="date"
                    className={inputClass}
                    value={data.enrollment_date}
                    onChange={(e) =>
                        setData('enrollment_date', e.target.value)
                    }
                />
                <InputError
                    message={errors.enrollment_date}
                    className="mt-1"
                />
            </div>

            <div>
                <InputLabel
                    htmlFor="educational_level_id"
                    value="Nivel educativo *"
                />
                <select
                    id="educational_level_id"
                    className={inputClass}
                    value={data.educational_level_id}
                    onChange={(e) =>
                        setData('educational_level_id', e.target.value)
                    }
                >
                    <option value="">Seleccione…</option>
                    {catalog.levels.map((l) => (
                        <option key={l.value} value={l.value}>
                            {l.label}
                        </option>
                    ))}
                </select>
                <InputError
                    message={errors.educational_level_id}
                    className="mt-1"
                />
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
                    onChange={(e) =>
                        setData('classroom_id', e.target.value)
                    }
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
                    onChange={(e) =>
                        setData('observations', e.target.value)
                    }
                />
                <InputError message={errors.observations} className="mt-1" />
            </div>
        </div>
    );
}
