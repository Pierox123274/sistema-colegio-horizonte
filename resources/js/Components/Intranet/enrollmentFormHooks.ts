import type { EnrollmentFormCatalog, EnrollmentFormState } from '@/types';
import type { InertiaFormProps } from '@inertiajs/react';
import { useEffect } from 'react';

export function useEnrollmentCatalogSync(
    form: InertiaFormProps<EnrollmentFormState>,
    catalog: EnrollmentFormCatalog,
): void {
    const { data, setData } = form;

    useEffect(() => {
        const grades = catalog.grades_by_level[data.educational_level_id] ?? [];
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
}
