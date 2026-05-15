import AuditActionBadge from '@/Components/Security/AuditActionBadge';
import AuditResultBadge from '@/Components/Security/AuditResultBadge';
import AuditSeverityBadge from '@/Components/Security/AuditSeverityBadge';
import { auditActionMeta } from '@/Components/Security/auditActionMeta';
import SecurityNetworkCell from '@/Components/Security/SecurityNetworkCell';
import SecurityUserCell from '@/Components/Security/SecurityUserCell';
import { Card } from '@/Components/Intranet/Card';
import { EmptyState } from '@/Components/Intranet/EmptyState';
import { ScrollText } from 'lucide-react';

export type AuditTimelineItem = {
    id: number;
    action: string;
    action_label: string;
    module_label: string;
    description: string | null;
    user?: { name: string; email: string } | null;
    user_role?: string | null;
    ip_address: string | null;
    browser: string;
    result: string;
    severity: string;
    created_at_label: string | null;
};

export default function AuditTimeline({
    items,
    title = 'Actividad reciente',
}: {
    items: AuditTimelineItem[];
    title?: string;
}) {
    if (items.length === 0) {
        return (
            <Card padding="none">
                <EmptyState
                    icon={ScrollText}
                    title="Sin actividad reciente"
                    description="Los eventos aparecerán aquí conforme se registren en el sistema."
                />
            </Card>
        );
    }

    return (
        <Card className="h-full">
            <h3 className="mb-5 border-b border-plomo/10 pb-3 text-sm font-bold text-navy-900">
                {title}
            </h3>
            <ol className="relative space-y-0">
                {items.map((item, index) => {
                    const meta = auditActionMeta(item.action);
                    const Icon = meta.icon;
                    const isLast = index === items.length - 1;

                    return (
                        <li key={item.id} className="relative flex gap-4 pb-8 last:pb-0">
                            {!isLast ? (
                                <span
                                    className="absolute left-5 top-11 bottom-0 w-px bg-plomo/20"
                                    aria-hidden
                                />
                            ) : null}
                            <span
                                className={`relative z-10 flex h-10 w-10 shrink-0 items-center justify-center rounded-full ring-4 ring-white ${meta.tone}`}
                            >
                                <Icon className="h-4 w-4" aria-hidden />
                            </span>
                            <div className="min-w-0 flex-1 space-y-2">
                                <div className="flex flex-wrap items-start justify-between gap-2">
                                    <div className="space-y-1">
                                        <div className="flex flex-wrap items-center gap-2">
                                            <AuditActionBadge
                                                action={item.action}
                                                label={item.action_label}
                                            />
                                            <span className="rounded-md bg-navy-50 px-2 py-0.5 text-xs font-medium text-navy-800">
                                                {item.module_label}
                                            </span>
                                        </div>
                                        <p className="text-xs text-plomo">
                                            {item.created_at_label}
                                        </p>
                                    </div>
                                    <div className="flex flex-wrap gap-1.5">
                                        <AuditSeverityBadge severity={item.severity} />
                                        <AuditResultBadge result={item.result} />
                                    </div>
                                </div>
                                <SecurityUserCell
                                    name={item.user?.name}
                                    email={item.user?.email}
                                    role={item.user_role}
                                />
                                {item.description ? (
                                    <p className="rounded-lg bg-navy-50/60 px-3 py-2 text-sm text-navy-900">
                                        {item.description}
                                    </p>
                                ) : null}
                                <SecurityNetworkCell
                                    ip={item.ip_address}
                                    browser={item.browser}
                                />
                            </div>
                        </li>
                    );
                })}
            </ol>
        </Card>
    );
}
