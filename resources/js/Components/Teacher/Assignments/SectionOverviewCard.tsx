import { RoleBadge } from '@/Components/Teacher/RoleBadge';
import type { SectionOverview } from '@/Components/Teacher/AssignmentsOverview';
import { Card } from '@/Components/Intranet/Card';
import { Users } from 'lucide-react';
import { SectionActionLinks } from '@/Components/Teacher/Assignments/SectionActionLinks';

type CourseChip = { id: number; name: string };

function CourseChips({ courses }: { courses: CourseChip[] }) {
    if (courses.length === 0) {
        return (
            <p className="mb-3 text-xs text-plomo">
                Sin curso específico en esta asignación
            </p>
        );
    }

    return (
        <div className="mb-3 flex flex-wrap gap-1.5">
            {courses.map((c) => (
                <span
                    key={c.id}
                    className="rounded-md bg-white px-2 py-0.5 text-xs font-medium text-navy-900 ring-1 ring-plomo/15"
                >
                    {c.name}
                </span>
            ))}
        </div>
    );
}

export function SectionOverviewCard({ section }: { section: SectionOverview }) {
    return (
        <Card
            key={section.section_id}
            className="flex flex-col border-l-4 border-l-navy-900/20"
        >
            <div className="mb-3 flex items-start justify-between gap-2">
                <div>
                    <p className="text-xs text-plomo">Sección {section.section}</p>
                    <h3 className="text-lg font-bold text-navy-900">{section.grade}</h3>
                    <p className="text-xs text-plomo">{section.level}</p>
                </div>
                <RoleBadge isTutor={section.is_tutor} />
            </div>

            <CourseChips courses={section.courses} />

            <p className="mb-4 flex items-center gap-1.5 text-sm text-plomo">
                <Users className="h-4 w-4" />
                {section.students_count} estudiantes matriculados
            </p>

            <div className="mt-auto border-t border-plomo/10 pt-4">
                <SectionActionLinks section={section} />
            </div>
        </Card>
    );
}
