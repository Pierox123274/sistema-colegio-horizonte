import { Quote } from 'lucide-react';
import { Reveal } from '@/Components/Public/Premium/Reveal';

type PublicTestimonialCardProps = {
    quote: string;
    name: string;
    role: string;
    org?: string;
    delay?: number;
};

export function PublicTestimonialCard({ quote, name, role, org, delay = 0 }: PublicTestimonialCardProps) {
    const initial = name.charAt(0);
    return (
        <Reveal delay={delay}>
            <article className="flex h-full flex-col rounded-2xl border border-slate-200/70 bg-white p-8 shadow-sm dark:border-white/10 dark:bg-slate-900/80">
                <Quote className="h-8 w-8 text-amber-500/80 dark:text-amber-400" />
                <p className="mt-4 flex-1 text-sm leading-relaxed text-slate-700 dark:text-slate-300">
                    “{quote}”
                </p>
                <div className="mt-6 flex items-center gap-3 border-t border-slate-100 pt-6 dark:border-white/10">
                    <span className="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-amber-400 to-amber-600 font-bold text-[#071526]">
                        {initial}
                    </span>
                    <div>
                        <p className="font-semibold text-slate-900 dark:text-white">{name}</p>
                        <p className="text-xs text-slate-600 dark:text-slate-400">{role}</p>
                        {org ? <p className="text-xs text-amber-600 dark:text-amber-400">{org}</p> : null}
                    </div>
                </div>
            </article>
        </Reveal>
    );
}
