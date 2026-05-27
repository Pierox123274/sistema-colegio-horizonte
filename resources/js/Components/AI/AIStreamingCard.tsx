import { Card } from '@/Components/Intranet/Card';
import { Loader2, Sparkles } from 'lucide-react';
import type { ReactNode } from 'react';

type Props = {
    title: string;
    loading?: boolean;
    children?: ReactNode;
    empty?: string;
};

export default function AIStreamingCard({ title, loading, children, empty }: Props) {
    return (
        <Card className="border border-slate-100 bg-gradient-to-br from-white to-slate-50/80">
            <h3 className="mb-3 flex items-center gap-2 text-sm font-semibold text-navy">
                <Sparkles className="h-4 w-4 text-brand-yellow" />
                {title}
            </h3>
            {loading ? (
                <div className="flex items-center gap-2 py-8 text-sm text-plomo">
                    <Loader2 className="h-5 w-5 animate-spin text-brand-yellow" />
                    Generando con IA institucional…
                </div>
            ) : children ? (
                <div className="text-sm text-slate-700">{children}</div>
            ) : (
                <p className="py-6 text-sm text-plomo">{empty ?? 'Complete el formulario y pulse Generar.'}</p>
            )}
        </Card>
    );
}
