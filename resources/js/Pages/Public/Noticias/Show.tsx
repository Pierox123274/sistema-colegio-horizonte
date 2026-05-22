import { Link } from '@inertiajs/react';
import { Calendar } from 'lucide-react';
import { PublicNewsCard, type NewsCardData } from '@/Components/Public/Premium/PublicNewsCard';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

type Article = {
    slug: string;
    title: string;
    excerpt: string;
    date: string;
    category: string;
    body: string;
    image: string;
    paragraphs?: string[];
};

type Props = {
    article: Article;
    related: NewsCardData[];
};

export default function NoticiaShow({ article, related }: Props) {
    const formatted = new Date(article.date).toLocaleDateString('es-PE', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
    const paragraphs = article.paragraphs ?? [article.body];

    return (
        <PublicLayout title={`${article.title} — Noticias`} description={article.excerpt}>
            <header className="relative min-h-[50vh] overflow-hidden">
                <img
                    src={article.image}
                    alt=""
                    className="absolute inset-0 h-full w-full object-cover"
                />
                <div className="absolute inset-0 bg-[#071526]/80" />
                <div className="absolute inset-0 bg-gradient-to-t from-[#071526] via-[#071526]/40 to-transparent" />
                <div className="relative mx-auto max-w-4xl px-4 pb-16 pt-28 sm:px-6 sm:pt-32 lg:px-8">
                    <nav className="mb-6 flex flex-wrap items-center gap-1 text-xs font-medium text-white/60">
                        <Link href={route('public.home')} className="hover:text-white">
                            Inicio
                        </Link>
                        <span>/</span>
                        <Link href={route('public.noticias')} className="hover:text-white">
                            Noticias
                        </Link>
                        <span>/</span>
                        <span className="text-amber-400">{article.category}</span>
                    </nav>
                    <span className="text-xs font-bold uppercase tracking-widest text-amber-400">
                        {article.category}
                    </span>
                    <h1 className="mt-3 font-display text-3xl font-extrabold text-white sm:text-4xl lg:text-5xl">
                        {article.title}
                    </h1>
                    <p className="mt-4 max-w-2xl text-lg text-slate-200">{article.excerpt}</p>
                    <p className="mt-6 inline-flex items-center gap-2 text-sm text-white/70">
                        <Calendar className="h-4 w-4 text-amber-400" />
                        {formatted}
                    </p>
                </div>
            </header>

            <article className="section-institutional py-16 sm:py-20">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <div className="space-y-6 text-lg leading-relaxed text-slate-700 dark:text-slate-300">
                        {paragraphs.map((p) => (
                            <p key={p.slice(0, 24)}>{p}</p>
                        ))}
                    </div>
                    <Link
                        href={route('public.noticias')}
                        className="mt-12 inline-flex text-sm font-semibold text-slate-900 hover:underline dark:text-amber-400"
                    >
                        ← Volver a noticias
                    </Link>
                </div>
            </article>

            {related.length > 0 ? (
                <section className="section-institutional-alt border-t border-slate-200/60 py-16 dark:border-white/10">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <h2 className="font-display text-2xl font-bold text-slate-900 dark:text-white">
                            Noticias relacionadas
                        </h2>
                        <div className="mt-8 grid gap-6 md:grid-cols-2">
                            {related.map((n, i) => (
                                <PublicNewsCard key={n.slug} {...n} delay={i * 0.06} />
                            ))}
                        </div>
                    </div>
                </section>
            ) : null}

            <InstitutionalCTA />
        </PublicLayout>
    );
}
