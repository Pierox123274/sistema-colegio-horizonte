import { CheckCircle2, XCircle } from 'lucide-react';

export default function LoginAttemptStatusBadge({
    successful,
    failureReason,
}: {
    successful: boolean;
    failureReason?: string | null;
}) {
    if (successful) {
        return (
            <span className="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 ring-1 ring-inset ring-emerald-200">
                <CheckCircle2 className="h-3.5 w-3.5" aria-hidden />
                Exitoso
            </span>
        );
    }

    return (
        <span
            className="inline-flex max-w-[12rem] items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-800 ring-1 ring-inset ring-rose-200"
            title={failureReason ?? undefined}
        >
            <XCircle className="h-3.5 w-3.5 shrink-0" aria-hidden />
            <span className="truncate">{failureReason ?? 'Fallido'}</span>
        </span>
    );
}
