import { motion } from 'framer-motion';
import { Link } from '@inertiajs/react';
import { ArrowRight, Sparkles } from 'lucide-react';
import type { ReactNode } from 'react';
import { publicImage } from '@/Components/Public/data/publicImages';
import { InstitutionalButtonLink } from '@/Components/Public/ui/InstitutionalButton';
import { PublicStatsRow } from './PublicStatsRow';

const HERO_PATTERN_OVERLAY =
    "opacity-[0.04] bg-[url('data:image/svg+xml,%3Csvg width=\\'60\\' height=\\'60\\' viewBox=\\'0 0 60 60\\' xmlns=\\'http://www.w3.org/2000/svg\\'%3E%3Cg fill=\\'%23ffffff\\'%3E%3Cpath d=\\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4z\\'/%3E%3C/g%3E%3C/svg%3E')]";

type PublicHeroImageProps = {
    badge?: string;
    title: ReactNode;
    subtitle: string;
    imageKey?: Parameters<typeof publicImage>[0];
    imageSrc?: string;
    primaryCta?: { label: string; href: string };
    secondaryCta?: { label: string; href: string };
    showStats?: boolean;
    compact?: boolean;
    statsItems?: { value: number; suffix?: string; label: string }[];
};

export function PublicHeroImage({
    badge = 'I.E.P. Horizonte · Lima',
    title,
    subtitle,
    imageKey = 'hero',
    imageSrc,
    primaryCta = { label: 'Admisión 2026', href: route('public.admision') },
    secondaryCta = { label: 'Explorar niveles', href: route('public.niveles') },
    showStats = true,
    compact = false,
    statsItems,
}: PublicHeroImageProps) {
    const src = imageSrc ?? publicImage(imageKey);
    const minH = compact ? 'min-h-[50vh]' : 'min-h-[92vh]';

    return (
        <section id="inicio" className={`relative overflow-hidden ${minH}`}>
            <img
                src={src}
                alt=""
                className="absolute inset-0 h-full w-full object-cover"
                fetchPriority="high"
            />
            <div className="absolute inset-0 bg-[#071526]/75" aria-hidden />
            <div
                className="absolute inset-0 bg-gradient-to-r from-[#071526]/95 via-[#071526]/70 to-[#071526]/40"
                aria-hidden
            />
            <div
                className="absolute inset-0 bg-gradient-to-t from-[#071526] via-transparent to-[#071526]/30"
                aria-hidden
            />
            <div
                className={`absolute inset-0 ${HERO_PATTERN_OVERLAY}`}
                aria-hidden
            />

            <div
                className={`relative mx-auto flex max-w-7xl flex-col justify-center px-4 sm:px-6 lg:px-8 ${
                    compact ? 'py-20 sm:py-24' : 'pb-24 pt-32 sm:pb-28 sm:pt-40 lg:pt-44'
                }`}
            >
                <div className="max-w-3xl">
                    <motion.p
                        initial={{ opacity: 0, y: 12 }}
                        animate={{ opacity: 1, y: 0 }}
                        className="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-amber-300 backdrop-blur-md"
                    >
                        <Sparkles className="h-3.5 w-3.5" />
                        {badge}
                    </motion.p>
                    <motion.h1
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.08 }}
                        className="mt-6 font-display text-4xl font-extrabold leading-[1.08] tracking-tight text-white sm:text-5xl lg:text-[3.25rem]"
                    >
                        {title}
                    </motion.h1>
                    <motion.p
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.16 }}
                        className="mt-6 max-w-xl text-lg leading-relaxed text-slate-200"
                    >
                        {subtitle}
                    </motion.p>
                    {!compact && primaryCta && secondaryCta ? (
                        <motion.div
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ delay: 0.24 }}
                            className="mt-10 flex flex-wrap gap-4"
                        >
                            <InstitutionalButtonLink href={primaryCta.href} variant="primary">
                                {primaryCta.label}
                                <ArrowRight className="h-4 w-4" />
                            </InstitutionalButtonLink>
                            <InstitutionalButtonLink href={secondaryCta.href} variant="secondary">
                                {secondaryCta.label}
                            </InstitutionalButtonLink>
                        </motion.div>
                    ) : null}
                </div>
            </div>

            {showStats && !compact ? (
                <PublicStatsRow variant="hero" items={statsItems} />
            ) : null}
        </section>
    );
}

/** Hero interno con imagen para páginas secundarias */
export function PublicPageHeroImage({
    title,
    subtitle,
    breadcrumbs,
    imageKey,
    imageSrc,
}: {
    title: string;
    subtitle?: string;
    breadcrumbs?: { label: string; href?: string }[];
    imageKey?: Parameters<typeof publicImage>[0];
    imageSrc?: string;
}) {
    const src = imageSrc ?? (imageKey ? publicImage(imageKey) : publicImage('hero'));

    return (
        <section className="relative min-h-[42vh] overflow-hidden">
            <img src={src} alt="" className="absolute inset-0 h-full w-full object-cover" />
            <div className="absolute inset-0 bg-[#071526]/80" />
            <div className="absolute inset-0 bg-gradient-to-t from-[#071526] to-transparent" />
            <div className="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 sm:py-24 lg:px-8">
                {breadcrumbs && breadcrumbs.length > 0 ? (
                    <nav className="mb-4 flex flex-wrap items-center gap-1 text-xs font-medium text-white/60">
                        <Link href={route('public.home')} className="hover:text-white">
                            Inicio
                        </Link>
                        {breadcrumbs.map((b, i) => (
                            <span key={i} className="flex items-center gap-1">
                                <span>/</span>
                                {b.href ? (
                                    <Link href={b.href} className="hover:text-white">
                                        {b.label}
                                    </Link>
                                ) : (
                                    <span className="text-amber-400">{b.label}</span>
                                )}
                            </span>
                        ))}
                    </nav>
                ) : null}
                <h1 className="font-display text-3xl font-extrabold text-white sm:text-4xl lg:text-5xl">
                    {title}
                </h1>
                {subtitle ? <p className="mt-4 max-w-2xl text-lg text-slate-200">{subtitle}</p> : null}
            </div>
        </section>
    );
}
