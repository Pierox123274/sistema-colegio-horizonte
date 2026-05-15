import { CheckCircle2, XCircle } from 'lucide-react';

export default function AuditResultBadge({ result }: { result: string }) {
    const ok = result === 'success';

    return (
        <span
            className={`inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset ${
                ok
                    ? 'bg-emerald-50 text-emerald-800 ring-emerald-200'
                    : 'bg-rose-50 text-rose-800 ring-rose-200'
            }`}
        >
            {ok ? (
                <CheckCircle2 className="h-3.5 w-3.5" aria-hidden />
            ) : (
                <XCircle className="h-3.5 w-3.5" aria-hidden />
            )}
            {ok ? 'Éxito' : 'Error'}
        </span>
    );
}
