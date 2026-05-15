const STATUS_CLASS: Record<string, string> = {
    registrado: 'bg-emerald-100 text-emerald-800 ring-emerald-200',
    anulado: 'bg-slate-100 text-slate-600 ring-slate-200',
};

export default function PaymentStatusBadge({
    status,
    label,
}: {
    status: string;
    label: string;
}) {
    const className =
        STATUS_CLASS[status] ?? 'bg-slate-100 text-slate-700 ring-slate-200';

    return (
        <span
            className={`inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset ${className}`}
        >
            {label}
        </span>
    );
}
