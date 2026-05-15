type RoleBadgeProps = {
    isTutor: boolean;
    compact?: boolean;
};

export function RoleBadge({ isTutor, compact = false }: RoleBadgeProps) {
    const label = isTutor ? 'Tutor de aula' : 'Docente de curso';

    return (
        <span
            className={`inline-flex items-center gap-1 rounded-full font-semibold ring-1 ring-inset ${
                compact ? 'px-2 py-0.5 text-[10px]' : 'px-2.5 py-0.5 text-xs'
            } ${
                isTutor
                    ? 'bg-amber-50 text-amber-900 ring-amber-200'
                    : 'bg-sky-50 text-sky-900 ring-sky-200'
            }`}
        >
            <span
                className={`h-1.5 w-1.5 rounded-full ${isTutor ? 'bg-amber-500' : 'bg-sky-500'}`}
                aria-hidden
            />
            {label}
        </span>
    );
}
