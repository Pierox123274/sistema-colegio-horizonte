import { CheckCircle2, CircleDashed } from 'lucide-react';

type ProviderCardProps = {
    label: string;
    provider: string;
    description: string;
    configured: boolean;
    status: string;
};

export default function ProviderCard({
    label,
    provider,
    description,
    configured,
}: ProviderCardProps) {
    return (
        <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-brand-yellow/60">
            <div className="mb-2 flex items-start justify-between gap-2">
                <h3 className="text-sm font-semibold text-navy">{label}</h3>
                {configured ? (
                    <span className="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold uppercase text-emerald-700">
                        <CheckCircle2 className="h-3 w-3" /> Configurado
                    </span>
                ) : (
                    <span className="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-plomo">
                        <CircleDashed className="h-3 w-3" /> Pendiente
                    </span>
                )}
            </div>
            <p className="text-xs text-plomo">{description}</p>
            <p className="mt-2 font-mono text-[10px] text-slate-500">provider: {provider}</p>
        </div>
    );
}
