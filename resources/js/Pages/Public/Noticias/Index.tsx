import { NewsMagazineSection } from '@/Components/Public/Premium/NewsMagazineSection';
import { PublicPageHeroImage } from '@/Components/Public/Premium/PublicHeroImage';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';
import type { PageProps } from '@/types';
import type { CmsHero, CmsNewsCard, CmsPageBrief } from '@/types/cms';
import { usePage } from '@inertiajs/react';

type Props = PageProps<{
    cmsPage?: CmsPageBrief | null;
    cmsHero?: CmsHero | null;
    news?: CmsNewsCard[];
}>;

export default function NoticiasIndex() {
    const { cmsPage, cmsHero, news } = usePage<Props>().props;

    return (
        <PublicLayout title="Noticias — I.E.P. Horizonte" description="Comunicados institucionales.">
            <PublicPageHeroImage
                title={cmsPage?.title ?? 'Noticias'}
                subtitle={
                    cmsPage?.subtitle ??
                    cmsHero?.subtitle ??
                    'Novedades, logros y comunicados de la comunidad Horizonte.'
                }
                imageKey="newsFeria"
                imageSrc={cmsHero?.image ?? undefined}
                breadcrumbs={[{ label: 'Noticias' }]}
            />
            <NewsMagazineSection showHeader={false} articles={news} />
            <InstitutionalCTA />
        </PublicLayout>
    );
}
