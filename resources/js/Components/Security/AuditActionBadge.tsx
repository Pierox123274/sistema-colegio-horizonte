import { auditActionMeta } from '@/Components/Security/auditActionMeta';

export default function AuditActionBadge({
    action,
    label,
}: {
    action: string;
    label: string;
}) {
    const meta = auditActionMeta(action);
    const Icon = meta.icon;

    return (
        <span
            className={`inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset ${meta.tone} ${meta.ring}`}
        >
            <Icon className="h-3.5 w-3.5 shrink-0" aria-hidden />
            {label}
        </span>
    );
}
