import { ShieldAlert, ShieldCheck } from 'lucide-react';

export default function SessionRiskBadge({ suspicious }: { suspicious: boolean }) {
    if (suspicious) {
        return (
            <span className="mt-1 inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-800 ring-1 ring-inset ring-rose-200">
                <ShieldAlert className="h-3.5 w-3.5" aria-hidden />
                Sospechosa
            </span>
        );
    }

    return (
        <span className="mt-1 inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 ring-1 ring-inset ring-emerald-200">
            <ShieldCheck className="h-3.5 w-3.5" aria-hidden />
            Normal
        </span>
    );
}
