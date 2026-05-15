import { Globe, Monitor } from 'lucide-react';

export default function SecurityNetworkCell({
    ip,
    browser,
}: {
    ip?: string | null;
    browser?: string | null;
}) {
    return (
        <div className="min-w-[8rem] space-y-1 text-xs">
            <p className="flex items-center gap-1.5 text-navy-900">
                <Globe className="h-3.5 w-3.5 shrink-0 text-plomo" aria-hidden />
                <span className="font-mono">{ip ?? '—'}</span>
            </p>
            <p className="flex items-center gap-1.5 text-plomo">
                <Monitor className="h-3.5 w-3.5 shrink-0" aria-hidden />
                <span>{browser ?? 'Desconocido'}</span>
            </p>
        </div>
    );
}
