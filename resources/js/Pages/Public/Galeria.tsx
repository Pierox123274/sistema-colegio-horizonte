import { galleryWithImages } from '@/Components/Public/data/publicSiteContent';
import { PublicGalleryGrid } from '@/Components/Public/Premium/PublicGalleryGrid';
import { PublicPageHeroImage } from '@/Components/Public/Premium/PublicHeroImage';
import { PublicSectionHeader } from '@/Components/Public/Premium/PublicSectionHeader';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';
import type { PageProps } from '@/types';
import type { CmsGalleryItem, CmsHero, CmsPageBrief } from '@/types/cms';
import { usePage } from '@inertiajs/react';

type Props = PageProps<{
    cmsPage?: CmsPageBrief | null;
    cmsHero?: CmsHero | null;
    galleryItems?: CmsGalleryItem[];
}>;

export default function Galeria() {
    const { cmsPage, cmsHero, galleryItems } = usePage<Props>().props;
    const items =
        galleryItems && galleryItems.length > 0 ? galleryItems : galleryWithImages();

    return (
        <PublicLayout title="Galería — I.E.P. Horizonte" description="Momentos del colegio.">
            <PublicPageHeroImage
                title={cmsPage?.title ?? 'Galería institucional'}
                subtitle={
                    cmsPage?.subtitle ??
                    cmsHero?.subtitle ??
                    'Campus, actividades, eventos y la vida diaria de nuestra comunidad.'
                }
                imageKey="hero"
                imageSrc={cmsHero?.image ?? undefined}
                breadcrumbs={[
                    { label: 'Vida escolar', href: route('public.vida-escolar') },
                    { label: 'Galería' },
                ]}
            />
            <section className="section-institutional py-16 sm:py-24">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <PublicSectionHeader
                        eyebrow="Momentos Horizonte"
                        title="Imágenes que cuentan nuestra historia"
                        description="Deporte, arte, ciencia y celebraciones en un recorrido visual por el colegio."
                        className="mb-12"
                    />
                    <PublicGalleryGrid items={items} />
                </div>
            </section>
            <InstitutionalCTA
                title="¿Quieres conocer el campus en persona?"
                primaryLabel="Agendar visita"
                primaryHref={route('public.admision')}
            />
        </PublicLayout>
    );
}
