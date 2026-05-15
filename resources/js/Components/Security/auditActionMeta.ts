import type { LucideIcon } from 'lucide-react';
import {
    Ban,
    Download,
    KeyRound,
    LogIn,
    LogOut,
    Pencil,
    Plus,
    Shield,
    Trash2,
} from 'lucide-react';

export type AuditActionKey = string;

const ACTION_META: Record<
    string,
    { icon: LucideIcon; tone: string; ring: string }
> = {
    login: { icon: LogIn, tone: 'bg-emerald-50 text-emerald-800', ring: 'ring-emerald-200' },
    logout: { icon: LogOut, tone: 'bg-slate-100 text-slate-700', ring: 'ring-slate-200' },
    create: { icon: Plus, tone: 'bg-sky-50 text-sky-900', ring: 'ring-sky-200' },
    update: { icon: Pencil, tone: 'bg-amber-50 text-amber-900', ring: 'ring-amber-200' },
    delete: { icon: Trash2, tone: 'bg-rose-50 text-rose-800', ring: 'ring-rose-200' },
    cancel: { icon: Ban, tone: 'bg-orange-50 text-orange-900', ring: 'ring-orange-200' },
    export: { icon: Download, tone: 'bg-violet-50 text-violet-900', ring: 'ring-violet-200' },
    permission_change: { icon: Shield, tone: 'bg-navy-50 text-navy-900', ring: 'ring-navy-200' },
    role_change: { icon: Shield, tone: 'bg-navy-50 text-navy-900', ring: 'ring-navy-200' },
};

const DEFAULT_META = {
    icon: KeyRound,
    tone: 'bg-navy-50 text-navy-900',
    ring: 'ring-navy-200',
};

export function auditActionMeta(action: AuditActionKey) {
    if (ACTION_META[action]) {
        return ACTION_META[action];
    }
    if (action.includes('cash') || action.includes('payment') || action.includes('sale')) {
        return { icon: Shield, tone: 'bg-celeste/15 text-navy-900', ring: 'ring-celeste/40' };
    }
    return DEFAULT_META;
}
