type Props = {
    status: 'ok' | 'warning' | 'critical' | string;
    label: string;
};

const styles: Record<string, string> = {
    ok: 'bg-emerald-50 text-emerald-800 border-emerald-200',
    warning: 'bg-amber-50 text-amber-900 border-amber-200',
    critical: 'bg-red-50 text-red-800 border-red-200',
};

export default function IntegrationHealthBadge({ status, label }: Props) {
    const cls = styles[status] ?? styles.warning;

    return (
        <span className={`inline-flex rounded-full border px-2 py-0.5 text-[10px] font-semibold uppercase ${cls}`}>
            {label}: {status}
        </span>
    );
}
