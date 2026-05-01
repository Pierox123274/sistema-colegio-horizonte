import { Link } from '@inertiajs/react';
import { demoNews } from '@/data/publicSiteDemo';

type NewsSectionProps = {
    limit?: number;
};

export function NewsSection({ limit }: NewsSectionProps) {
    const items = limit ? demoNews.slice(0, limit) : demoNews;

    return (
        <section className="bg-white py-16 sm:py-24" id="noticias">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p className="text-xs font-bold uppercase tracking-widest text-brand-red">
                            Noticias
                        </p>
                        <h2 className="mt-2 text-3xl font-bold tracking-tight text-navy-900 sm:text-4xl">
                            Vida en el colegio
                        </h2>
                        <p className="mt-2 max-w-xl text-plomo">
                            Novedades y comunicados (contenido de demostración).
                        </p>
                    </div>
                    <Link
                        href={route('public.noticias')}
                        className="text-sm font-semibold text-navy-900 underline-offset-4 hover:underline"
                    >
                        Ver todas
                    </Link>
                </div>
                <div className="mt-12 grid gap-6 md:grid-cols-3">
                    {items.map((n) => (
                        <article
                            key={n.id}
                            className="group flex flex-col rounded-2xl border border-plomo/10 bg-navy-50/30 p-6 transition duration-300 hover:-translate-y-0.5 hover:border-brand-yellow/40 hover:shadow-md"
                        >
                            <span className="w-fit rounded-full bg-white px-2.5 py-0.5 text-xs font-semibold text-brand-red ring-1 ring-brand-red/20">
                                {n.tag}
                            </span>
                            <time className="mt-3 text-xs font-medium text-plomo">
                                {n.date}
                            </time>
                            <h3 className="mt-2 text-lg font-bold text-navy-900 transition group-hover:text-brand-red">
                                {n.title}
                            </h3>
                            <p className="mt-2 flex-1 text-sm leading-relaxed text-plomo">
                                {n.excerpt}
                            </p>
                            <span className="mt-4 text-sm font-semibold text-navy-900">
                                Leer más →
                            </span>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
