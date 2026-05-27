type Props = {
    status: string;
};

const tones: Record<string, string> = {
    scheduled: 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-200',
    live: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
    completed: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
    cancelled: 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-200',
};

const labels: Record<string, string> = {
    scheduled: 'Programada',
    live: 'En curso',
    completed: 'Finalizada',
    cancelled: 'Cancelada',
};

export default function MeetingStatusBadge({ status }: Props) {
    return (
        <span
            className={`inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide ${tones[status] ?? tones.scheduled}`}
        >
            {labels[status] ?? status}
        </span>
    );
}
