import { Loader2, Send, Sparkles } from 'lucide-react';
import type { FormEvent } from 'react';

type Props = {
    value: string;
    onChange: (value: string) => void;
    onSubmit: () => void;
    loading?: boolean;
    placeholder?: string;
    disabled?: boolean;
    label?: string;
};

export default function AIPromptBox({
    value,
    onChange,
    onSubmit,
    loading = false,
    placeholder = 'Describe qué necesitas generar…',
    disabled = false,
    label = 'Instrucción para la IA',
}: Props) {
    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        if (!disabled && !loading && value.trim()) {
            onSubmit();
        }
    };

    return (
        <form onSubmit={handleSubmit} className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <label className="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-plomo">
                <Sparkles className="h-3.5 w-3.5 text-brand-yellow" />
                {label}
            </label>
            <textarea
                value={value}
                onChange={(e) => onChange(e.target.value)}
                rows={3}
                disabled={disabled || loading}
                placeholder={placeholder}
                className="w-full resize-none rounded-lg border border-slate-200 px-3 py-2 text-sm text-navy focus:border-brand-yellow focus:outline-none focus:ring-1 focus:ring-brand-yellow disabled:bg-slate-50"
            />
            <div className="mt-3 flex justify-end">
                <button
                    type="submit"
                    disabled={disabled || loading || !value.trim()}
                    className="inline-flex items-center gap-2 rounded-lg bg-navy px-4 py-2 text-sm font-semibold text-white hover:bg-navy/90 disabled:opacity-50"
                >
                    {loading ? <Loader2 className="h-4 w-4 animate-spin" /> : <Send className="h-4 w-4" />}
                    Generar
                </button>
            </div>
        </form>
    );
}
