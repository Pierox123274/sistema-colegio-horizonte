import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import { DOCUMENT_TYPE_LABELS } from '@/lib/studentLabels';
import type { EnrollmentStudentPreview } from '@/types';
import { Link } from '@inertiajs/react';
import { Loader2, Search, User } from 'lucide-react';
import type { RefObject } from 'react';
import type { EnrollmentStudentSearchHit } from '@/types';

const inputClass =
    'mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900';

type Props = {
    studentError?: string;
    selectedStudent: EnrollmentStudentPreview | null;
    showSearchUi: boolean;
    searchWrapRef: RefObject<HTMLDivElement | null>;
    searchText: string;
    onSearchTextChange: (value: string) => void;
    onSearchFocus: () => void;
    searchMin: number;
    searchLoading: boolean;
    searchError: string | null;
    searchOpen: boolean;
    searchHits: EnrollmentStudentSearchHit[];
    onPickStudent: (hit: EnrollmentStudentSearchHit) => void;
    pickLoading: boolean;
    onChangeStudent: () => void;
};

function documentLabel(student: EnrollmentStudentPreview): string | null {
    if (student.document_type == null || student.document_type === '') {
        return null;
    }
    return DOCUMENT_TYPE_LABELS[student.document_type] ?? student.document_type;
}

function SelectedStudentCard({
    student,
    onChangeStudent,
}: {
    student: EnrollmentStudentPreview;
    onChangeStudent: () => void;
}) {
    const docLabel = documentLabel(student);

    return (
        <div className="mt-2 space-y-3">
            <div className="rounded-xl border border-plomo/15 bg-gradient-to-br from-navy-50/90 to-white p-4 shadow-sm ring-1 ring-plomo/10">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between sm:gap-6">
                    <div className="flex min-w-0 flex-1 gap-3">
                        <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-navy-900 text-white">
                            <User className="h-5 w-5" aria-hidden />
                        </div>
                        <div className="min-w-0">
                            <p className="font-semibold text-navy-900">
                                {student.first_name} {student.last_name}
                            </p>
                            <p className="font-mono text-xs text-plomo">{student.code}</p>
                            <p className="mt-1 text-sm text-navy-800">
                                {docLabel ? `${docLabel}: ` : ''}
                                {student.document_number ?? '—'}
                            </p>
                        </div>
                    </div>
                    <div className="flex w-full shrink-0 flex-col gap-2 sm:w-auto sm:flex-row sm:items-center sm:justify-end sm:gap-2">
                        <Link
                            href={route('intranet.students.show', student.id)}
                            className="inline-flex w-full items-center justify-center rounded-lg border border-plomo/25 bg-white px-3 py-2 text-center text-xs font-semibold text-navy-900 shadow-sm ring-1 ring-plomo/10 transition hover:border-plomo/35 hover:bg-navy-50 sm:w-auto sm:min-w-[7.5rem]"
                        >
                            Ver ficha
                        </Link>
                        <button
                            type="button"
                            onClick={onChangeStudent}
                            className="inline-flex w-full items-center justify-center rounded-lg border border-navy-900/15 bg-navy-900 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-navy-950 sm:w-auto sm:min-w-[10rem]"
                        >
                            Cambiar estudiante
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}

export function EnrollmentStudentPicker({
    studentError,
    selectedStudent,
    showSearchUi,
    searchWrapRef,
    searchText,
    onSearchTextChange,
    onSearchFocus,
    searchMin,
    searchLoading,
    searchError,
    searchOpen,
    searchHits,
    onPickStudent,
    pickLoading,
    onChangeStudent,
}: Props) {
    return (
        <div className="sm:col-span-2">
            <InputLabel value="Estudiante *" />

            {selectedStudent ? (
                <SelectedStudentCard student={selectedStudent} onChangeStudent={onChangeStudent} />
            ) : null}

            {showSearchUi ? (
                <div ref={searchWrapRef} className="relative mt-2">
                    <div className="relative">
                        <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-plomo" />
                        <input
                            id="student_search"
                            type="search"
                            autoComplete="off"
                            placeholder={`Buscar por código, nombre, apellidos o documento (mín. ${searchMin} caracteres)`}
                            className={`${inputClass} pl-10 pr-10`}
                            value={searchText}
                            onChange={(e) => onSearchTextChange(e.target.value)}
                            onFocus={onSearchFocus}
                            aria-expanded={searchOpen}
                            aria-controls="student-search-results"
                            aria-autocomplete="list"
                        />
                        {searchLoading ? (
                            <Loader2 className="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 animate-spin text-plomo" />
                        ) : null}
                    </div>
                    <p className="mt-1 text-xs text-plomo">
                        Escriba al menos {searchMin} caracteres para ver coincidencias (máx. 20 resultados).
                    </p>

                    {searchError ? (
                        <p className="mt-2 text-sm text-rose-700">{searchError}</p>
                    ) : null}

                    {searchOpen && searchText.trim().length >= searchMin && !searchLoading ? (
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
                                        onMouseDown={(e) => e.preventDefault()}
                                        onClick={() => onPickStudent(hit)}
                                    >
                                        <span className="font-medium text-navy-900">
                                            {hit.first_name} {hit.last_name}
                                        </span>
                                        <span className="font-mono text-xs text-plomo">
                                            {hit.code}
                                            {hit.document_number ? ` · ${hit.document_number}` : ''}
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

            <InputError message={studentError} className="mt-1" />
        </div>
    );
}
