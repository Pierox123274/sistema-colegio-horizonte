import type {
    EnrollmentStudentPreview,
    EnrollmentStudentSearchHit,
} from '@/types';
import type { InertiaFormProps } from '@inertiajs/react';
import axios from 'axios';
import { useCallback, useEffect, useRef, useState } from 'react';
import type { EnrollmentFormState } from '@/types';

const SEARCH_MIN = 2;
const DEBOUNCE_MS = 320;

export function useEnrollmentStudentPicker(
    form: InertiaFormProps<EnrollmentFormState>,
    initialStudentPreview: EnrollmentStudentPreview | null,
) {
    const { setData } = form;

    const [preview, setPreview] = useState<EnrollmentStudentPreview | null>(
        initialStudentPreview,
    );
    const [changingStudent, setChangingStudent] = useState(false);
    const [searchText, setSearchText] = useState('');
    const [searchHits, setSearchHits] = useState<EnrollmentStudentSearchHit[]>([]);
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
                .then((res) => setSearchHits(res.data.students ?? []))
                .catch(() => {
                    setSearchError('No se pudo cargar la búsqueda.');
                    setSearchHits([]);
                })
                .finally(() => setSearchLoading(false));
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
                    route('intranet.enrollments.students.preview', hit.id),
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

    const startChangeStudent = useCallback(() => {
        setChangingStudent(true);
        setPreview(null);
        setData('student_id', '');
        setData('guardian_id', '');
        setSearchText('');
        setSearchHits([]);
        setSearchOpen(false);
    }, [setData]);

    return {
        preview,
        changingStudent,
        searchText,
        setSearchText,
        searchHits,
        searchLoading,
        searchOpen,
        setSearchOpen,
        searchError,
        pickLoading,
        searchWrapRef,
        pickStudent,
        startChangeStudent,
        searchMin: SEARCH_MIN,
    };
}
