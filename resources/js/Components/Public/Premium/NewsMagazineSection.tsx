import { useState } from 'react';
import { PublicNewsCard } from './PublicNewsCard';
import { PublicSectionHeader } from './PublicSectionHeader';
import { newsWithImages } from '@/Components/Public/data/publicSiteContent';

const categories = ['Todas', 'Admisión', 'Vida escolar', 'Logros'];

export function NewsMagazineSection({
    showHeader = true,
    compact = false,
}: {
    showHeader?: boolean;
    compact?: boolean;
}) {
    const allArticles = newsWithImages();
    const [filter, setFilter] = useState('Todas');
    const filtered =
        filter === 'Todas' ? allArticles : allArticles.filter((a) => a.category === filter);
    const articles = compact ? allArticles.slice(0, 3) : filtered;
    const [featured, ...rest] = articles;

    return (
        <section className="section-institutional py-16 sm:py-24">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                {showHeader ? (
                    <PublicSectionHeader
                        eyebrow="Noticias"
                        title="Vida en el colegio"
                        description="Comunicados, logros y novedades de nuestra comunidad."
                        className="mb-10"
                    />
                ) : null}

                {!compact ? (
                <div className="mb-10 flex flex-wrap gap-2">
                    {categories.map((cat) => (
                        <button
                            key={cat}
                            type="button"
                            onClick={() => setFilter(cat)}
                            className={`rounded-full px-4 py-2 text-sm font-semibold transition ${
                                filter === cat
                                    ? 'bg-[#0f2847] text-amber-400 dark:bg-amber-400/20 dark:text-amber-300'
                                    : 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-white/10 dark:text-slate-300 dark:hover:bg-white/15'
                            }`}
                        >
                            {cat}
                        </button>
                    ))}
                </div>
                ) : (
                    <PublicSectionHeader
                        eyebrow="Noticias"
                        title="Vida en el colegio"
                        description="Comunicados, logros y novedades de nuestra comunidad."
                        className="mb-10"
                    />
                )}

                {featured ? <PublicNewsCard {...featured} featured /> : null}
                {rest.length > 0 ? (
                    <div className="mt-8 grid gap-6 md:grid-cols-2">
                        {rest.map((n, i) => (
                            <PublicNewsCard key={n.slug} {...n} delay={i * 0.06} />
                        ))}
                    </div>
                ) : null}

                {compact ? (
                    <p className="mt-10 text-center">
                        <a
                            href={route('public.noticias')}
                            className="text-sm font-semibold text-slate-900 hover:underline dark:text-amber-400"
                        >
                            Ver todas las noticias →
                        </a>
                    </p>
                ) : null}
            </div>
        </section>
    );
}
