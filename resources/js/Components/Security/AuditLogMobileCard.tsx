import AuditActionBadge from '@/Components/Security/AuditActionBadge';
import AuditResultBadge from '@/Components/Security/AuditResultBadge';
import AuditSeverityBadge from '@/Components/Security/AuditSeverityBadge';
import SecurityNetworkCell from '@/Components/Security/SecurityNetworkCell';
import SecurityUserCell from '@/Components/Security/SecurityUserCell';
import { Card } from '@/Components/Intranet/Card';
import type { AuditTimelineItem } from '@/Components/Security/AuditTimeline';

export default function AuditLogMobileCard({ row }: { row: AuditTimelineItem }) {
    return (
        <Card padding="sm" className="space-y-3">
            <div className="flex flex-wrap items-center justify-between gap-2">
                <p className="text-xs font-medium text-plomo">{row.created_at_label}</p>
                <AuditSeverityBadge severity={row.severity} />
            </div>
            <div className="flex flex-wrap gap-2">
                <AuditActionBadge action={row.action} label={row.action_label} />
                <span className="rounded-md bg-navy-50 px-2 py-0.5 text-xs font-medium text-navy-800">
                    {row.module_label}
                </span>
                <AuditResultBadge result={row.result} />
            </div>
            <SecurityUserCell
                name={row.user?.name}
                email={row.user?.email}
                role={row.user_role}
            />
            {row.description ? (
                <p className="text-sm text-navy-800">{row.description}</p>
            ) : null}
            <SecurityNetworkCell ip={row.ip_address} browser={row.browser} />
        </Card>
    );
}
