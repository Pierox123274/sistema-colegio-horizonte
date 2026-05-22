import { Link } from '@inertiajs/react';
import { ArrowRight, Calendar } from 'lucide-react';
import { Reveal } from '@/Components/Public/Premium/Reveal';

export type NewsCardData = {
    slug: string;
    title: string;
    excerpt: string;
    date: string;
    category: string;
    image: string;
};

type PublicNewsCardProps = NewsCardData & {
    featured?: boolean;
    delay?: number;
};

export function PublicNewsCard({
    slug,
    title,
    excerpt,
    date,
    category,
    image,
    featured = false,
    delay = 0,
}: PublicNewsCardProps) {
    const formatted = new Date(date).toLocaleDateString('es-PE', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });

    if (featured) {
        return (
            <Reveal delay={delay}>
                <Link
                    href={route('public.noticias.show', { slug })}
                    className="group grid overflow-hidden rounded-3xl border border-slate-200/70 bg-white shadow-lg transition hover:shadow-xl dark:border-white/10 dark:bg-slate-900/80 md:grid-cols-2"
                >
                    <div className="relative min-h-[16rem] md:min-h-full">
                        <img
                            src={image}
                            alt={title}
                            className="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105"
                        />
                    </div>
                    <div className="flex flex-col justify-center p-8 sm:p-10">
                        <span className="text-xs font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400">
                            {category} · Destacada
                        </span>
                        <h2 className="mt-3 font-display text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">
                            {title}
                        </h2>
                        <p className="mt-4 text-slate-600 dark:text-slate-300">{excerpt}</p>
                        <div className="mt-6 flex items-center justify-between">
                            <span className="inline-flex items-center gap-1.5 text-sm text-slate-500">
                                <Calendar className="h-4 w-4" />
                                {formatted}
                            </span>
                            <span className="inline-flex items-center gap-1 text-sm font-semibold text-slate-900 group-hover:gap-2 dark:text-amber-400">
                                Leer artículo
                                <ArrowRight className="h-4 w-4" />
                            </span>
                        </div>
                    </div>
                </Link>
            </Reveal>
        );
    }

    return (
        <Reveal delay={delay}>
            <article className="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200/70 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg dark:border-white/10 dark:bg-slate-900/80">
                <div className="relative aspect-[16/10] overflow-hidden">
                    <img
                        src={image}
                        alt={title}
                        className="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                        loading="lazy"
                    />
                    <span className="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1 text-xs font-bold text-slate-900 shadow dark:bg-[#071526]/90 dark:text-amber-400">
                        {category}
                    </span>
                </div>
                <div className="flex flex-1 flex-col p-6">
                    <time className="text-xs text-slate-500">{formatted}</time>
                    <h3 className="mt-2 font-display text-lg font-bold text-slate-900 dark:text-white">
                        {title}
                    </h3>
                    <p className="mt-2 flex-1 text-sm text-slate-600 dark:text-slate-400">{excerpt}</p>
                    <Link
                        href={route('public.noticias.show', { slug })}
                        className="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-slate-900 transition group-hover:gap-2 dark:text-amber-400"
                    >
                        Leer más
                        <ArrowRight className="h-4 w-4" />
                    </Link>
                </div>
            </article>
        </Reveal>
    );
}
