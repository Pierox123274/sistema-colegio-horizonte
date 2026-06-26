import type { SectionOverview } from '@/Components/Teacher/AssignmentsOverview';
import { Link } from '@inertiajs/react';
import { CalendarCheck, ClipboardCheck, Users } from 'lucide-react';

export function SectionActionLinks({ section }: { section: SectionOverview }) {
    return (
        <div className="flex flex-wrap gap-2">
            <Link
                href={section.links.students}
                className="inline-flex items-center gap-1.5 rounded-lg border border-plomo/20 bg-white px-3 py-2 text-xs font-semibold text-navy-900 shadow-sm transition hover:bg-navy-50"
            >
                <Users className="h-3.5 w-3.5" />
                Ver estudiantes
            </Link>
            <Link
                href={section.links.attendance}
                className="inline-flex items-center gap-1.5 rounded-lg border border-plomo/20 bg-white px-3 py-2 text-xs font-semibold text-navy-900 shadow-sm transition hover:bg-navy-50"
            >
                <CalendarCheck className="h-3.5 w-3.5" />
                Registrar asistencia
            </Link>
            <Link
                href={section.links.grades}
                className="inline-flex items-center gap-1.5 rounded-lg bg-navy-900 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-navy-950"
            >
                <ClipboardCheck className="h-3.5 w-3.5" />
                Registrar notas
            </Link>
        </div>
    );
}
