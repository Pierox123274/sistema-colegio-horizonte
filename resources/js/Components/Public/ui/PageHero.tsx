import { PublicPageHeroImage } from '@/Components/Public/Premium/PublicHeroImage';
import type { PageProps } from '@/types';
import type { CmsHero, CmsPageBrief } from '@/types/cms';
import { usePage } from '@inertiajs/react';
import { Breadcrumbs, type BreadcrumbItem } from './Breadcrumbs';

type PageHeroProps = {
    title: string;
    subtitle?: string;
    breadcrumbs: BreadcrumbItem[];
    compact?: boolean;
};

type CmsPageProps = PageProps & {
    cmsPage?: CmsPageBrief | null;
    cmsHero?: CmsHero | null;
};

export function PageHero({ title, subtitle, breadcrumbs, compact = false }: PageHeroProps) {
    const { cmsPage, cmsHero } = usePage<CmsPageProps>().props;
    const resolvedTitle = cmsPage?.title ?? title;
    const resolvedSubtitle = cmsHero?.subtitle ?? cmsPage?.subtitle ?? subtitle;
    const imageSrc = cmsHero?.image ?? undefined;

    if (imageSrc) {
        return (
            <PublicPageHeroImage
                title={resolvedTitle}
                subtitle={resolvedSubtitle ?? ''}
                imageSrc={imageSrc}
                breadcrumbs={breadcrumbs}
            />
        );
    }

    return (
        <div className="relative overflow-hidden border-b border-slate-200/60 bg-gradient-to-br from-institutional-blue-950 via-institutional-blue-900 to-institutional-blue-800 text-white dark:border-white/10">
            <div
                className="absolute inset-0 bg-[radial-gradient(ellipse_70%_50%_at_50%_-20%,rgba(201,162,39,0.15),transparent)]"
                aria-hidden
            />
            <div
                className={`relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 ${compact ? 'py-12 sm:py-14' : 'py-14 sm:py-20'}`}
            >
                <Breadcrumbs items={breadcrumbs} light />
                <h1 className="mt-4 font-display text-3xl font-extrabold tracking-tight sm:text-4xl lg:text-5xl">
                    {resolvedTitle}
                </h1>
                {resolvedSubtitle ? (
                    <p className="mt-4 max-w-2xl text-base leading-relaxed text-white/78 sm:text-lg">
                        {resolvedSubtitle}
                    </p>
                ) : null}
            </div>
        </div>
    );
}
