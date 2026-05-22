import { Link } from '@inertiajs/react';
import { ArrowRight, Calendar } from 'lucide-react';

type NewsCardProps = {
    slug: string;
    title: string;
    excerpt: string;
    date: string;
    category: string;
};

export function NewsCard({ slug, title, excerpt, date, category }: NewsCardProps) {
    return (
        <article className="group flex flex-col rounded-2xl border border-slate-200/70 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-institutional-gold/30 hover:shadow-institutional dark:border-white/10 dark:bg-slate-900/60">
            <span className="text-xs font-bold uppercase tracking-wider text-institutional-gold">
                {category}
            </span>
            <h3 className="mt-2 font-display text-lg font-bold text-institutional-blue-900 dark:text-white">
                {title}
            </h3>
            <p className="mt-2 flex-1 text-sm text-plomo dark:text-slate-400">{excerpt}</p>
            <div className="mt-4 flex items-center justify-between">
                <span className="inline-flex items-center gap-1 text-xs text-slate-500">
                    <Calendar className="h-3.5 w-3.5" />
                    {new Date(date).toLocaleDateString('es-PE', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric',
                    })}
                </span>
                <Link
                    href={route('public.noticias.show', { slug })}
                    className="inline-flex items-center gap-1 text-sm font-semibold text-institutional-blue-900 transition group-hover:gap-2 dark:text-institutional-gold"
                >
                    Leer
                    <ArrowRight className="h-4 w-4" />
                </Link>
            </div>
        </article>
    );
}
