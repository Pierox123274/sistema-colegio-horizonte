import { levelContent, type LevelKey } from '@/Components/Public/data/publicSiteContent';
import { levelHeroImage, publicImage } from '@/Components/Public/data/publicImages';
import { PublicImageCard } from '@/Components/Public/Premium/PublicImageCard';
import { PublicPageHeroImage } from '@/Components/Public/Premium/PublicHeroImage';
import { PublicSectionHeader } from '@/Components/Public/Premium/PublicSectionHeader';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';
import type { PageProps } from '@/types';
import type { CmsHero, CmsPageBrief } from '@/types/cms';
import { usePage } from '@inertiajs/react';

type LevelPageProps = PageProps & {
    level: LevelKey;
    cmsPage?: CmsPageBrief | null;
    cmsHero?: CmsHero | null;
};

export default function LevelPage({ level }: { level: LevelKey }) {
    const { cmsPage, cmsHero } = usePage<LevelPageProps>().props;
    const data = levelContent[level];
    const heroKey = levelHeroImage[level];

    return (
        <PublicLayout title={`${data.title} — I.E.P. Horizonte`} description={data.tagline}>
            <PublicPageHeroImage
                title={cmsPage?.title ?? data.title}
                subtitle={cmsHero?.subtitle ?? cmsPage?.subtitle ?? data.tagline}
                imageKey={heroKey}
                imageSrc={cmsHero?.image ?? undefined}
                breadcrumbs={[
                    { label: 'Niveles', href: route('public.niveles') },
                    { label: data.title.replace('Nivel ', '') },
                ]}
            />
            <section className="section-institutional py-16 sm:py-24">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="grid gap-10 lg:grid-cols-2 lg:items-start">
                        <div>
                            <PublicSectionHeader
                                eyebrow="Propuesta académica"
                                title="Formación con estándares de excelencia"
                                className="mb-6"
                            />
                            <p className="text-plomo leading-relaxed dark:text-slate-300">
                                {data.proposal}
                            </p>
                            <p className="mt-4 text-sm font-semibold text-amber-600 dark:text-amber-400">
                                {data.grades} · {data.ages}
                            </p>
                        </div>
                        <PublicImageCard
                            image={publicImage(heroKey)}
                            title={data.title}
                            subtitle={data.grades}
                            aspect="aspect-[5/4] min-h-[16rem]"
                        />
                    </div>

                    <div className="mt-16 grid gap-8 lg:grid-cols-2">
                        <div className="rounded-2xl border border-slate-200/70 bg-white p-8 shadow-sm dark:border-white/10 dark:bg-slate-900/80">
                            <h3 className="font-display text-xl font-bold text-slate-900 dark:text-white">
                                Metodología
                            </h3>
                            <ul className="mt-4 space-y-3">
                                {data.methodology.map((m) => (
                                    <li
                                        key={m}
                                        className="flex gap-3 text-sm text-slate-600 dark:text-slate-300"
                                    >
                                        <span className="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-amber-500" />
                                        {m}
                                    </li>
                                ))}
                            </ul>
                        </div>
                        <div className="rounded-2xl border border-slate-200/70 bg-institutional-surface-alt p-8 dark:border-white/10">
                            <h3 className="font-display text-xl font-bold text-slate-900 dark:text-white">
                                Perfil del estudiante
                            </h3>
                            <p className="mt-4 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                                {data.profile}
                            </p>
                            <h4 className="mt-8 text-sm font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                Actividades destacadas
                            </h4>
                            <ul className="mt-3 flex flex-wrap gap-2">
                                {data.activities.map((a) => (
                                    <li
                                        key={a}
                                        className="rounded-full border border-slate-200/80 bg-white px-4 py-2 text-sm font-medium dark:border-white/10 dark:bg-slate-950/60"
                                    >
                                        {a}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
            <InstitutionalCTA
                title={`¿Tu hijo/a ingresará a ${data.title.replace('Nivel ', '')}?`}
                description="Conoce el proceso de admisión 2026 y agenda una visita guiada."
                primaryLabel="Postular a admisión"
                primaryHref={route('public.admision')}
            />
        </PublicLayout>
    );
}
