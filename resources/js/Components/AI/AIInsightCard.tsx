import { Card } from '@/Components/Intranet/Card';
import { Sparkles } from 'lucide-react';

type Props = {
    title: string;
    body: string;
    badge?: string;
};

export default function AIInsightCard({ title, body, badge }: Props) {
    return (
        <Card className="border-l-4 border-l-brand-yellow">
            <div className="mb-2 flex items-center justify-between gap-2">
                <h3 className="flex items-center gap-2 text-sm font-semibold text-navy">
                    <Sparkles className="h-4 w-4 text-brand-yellow" />
                    {title}
                </h3>
                {badge && (
                    <span className="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-plomo">
                        {badge}
                    </span>
                )}
            </div>
            <p className="whitespace-pre-wrap text-sm text-slate-700">{body}</p>
        </Card>
    );
}
