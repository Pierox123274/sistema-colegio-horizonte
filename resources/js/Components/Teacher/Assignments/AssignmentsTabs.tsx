import { router } from '@inertiajs/react';
import { BookMarked, ClipboardList, LayoutGrid, Users } from 'lucide-react';

export type AssignmentsTabId = 'resumen' | 'secciones' | 'cursos' | 'estudiantes';

const TABS: Array<{
    id: AssignmentsTabId;
    label: string;
    icon: typeof LayoutGrid;
}> = [
    { id: 'resumen', label: 'Resumen', icon: LayoutGrid },
    { id: 'secciones', label: 'Mis secciones', icon: Users },
    { id: 'cursos', label: 'Mis cursos', icon: BookMarked },
    { id: 'estudiantes', label: 'Mis estudiantes', icon: ClipboardList },
];

type Props = {
    active: AssignmentsTabId;
};

export default function AssignmentsTabs({ active }: Props) {
    const go = (tab: AssignmentsTabId) => {
        router.get(
            route('teacher.assignments.index'),
            { tab },
            { preserveState: true, replace: true },
        );
    };

    return (
        <nav
            className="mb-6 flex flex-col gap-2 sm:flex-row sm:flex-wrap"
            aria-label="Secciones de mis asignaciones"
        >
            {TABS.map(({ id, label, icon: Icon }) => {
                const isActive = active === id;
                return (
                    <button
                        key={id}
                        type="button"
                        onClick={() => go(id)}
                        className={`inline-flex items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold transition ${
                            isActive
                                ? 'border-navy-900 bg-navy-900 text-white shadow-sm'
                                : 'border-plomo/20 bg-white text-navy-900 hover:border-navy-900/30 hover:bg-navy-50/60'
                        }`}
                        aria-current={isActive ? 'page' : undefined}
                    >
                        <Icon className="h-4 w-4 shrink-0" strokeWidth={1.75} />
                        {label}
                    </button>
                );
            })}
        </nav>
    );
}
