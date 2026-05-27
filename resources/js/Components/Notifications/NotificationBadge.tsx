type Props = {
    priority: string;
};

const toneByPriority: Record<string, string> = {
    low: 'bg-slate-100 text-slate-700',
    medium: 'bg-sky-100 text-sky-700',
    high: 'bg-amber-100 text-amber-800',
    critical: 'bg-red-100 text-red-700',
};

export default function NotificationBadge({ priority }: Props) {
    return (
        <span
            className={`rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide ${toneByPriority[priority] ?? toneByPriority.medium}`}
        >
            {priority}
        </span>
    );
}
