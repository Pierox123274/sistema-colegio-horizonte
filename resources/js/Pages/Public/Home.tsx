import { EducationalInnovationSection } from '@/Components/Public/Institutional/EducationalInnovationSection';
import { HomeSectionTeaser } from '@/Components/Public/Home/HomeSectionTeaser';
import { missionVision } from '@/Components/Public/institutionalSiteData';
import { navHref, nivelesMenu } from '@/Components/Public/nav/publicNavConfig';
import { CommunityTestimonialsSection } from '@/Components/Public/Premium/CommunityTestimonialsSection';
import { NewsMagazineSection } from '@/Components/Public/Premium/NewsMagazineSection';
import { PublicHeroImage } from '@/Components/Public/Premium/PublicHeroImage';
import { SchoolLifeEditorialSection } from '@/Components/Public/Premium/SchoolLifeEditorialSection';
import { LevelCard } from '@/Components/Public/ui/LevelCard';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';
import { cmsRoute } from '@/lib/cmsRoute';
import type { PageProps } from '@/types';
import type { CmsHero, CmsNewsCard, CmsSection, CmsTestimonial } from '@/types/cms';
import { usePage } from '@inertiajs/react';

type HomeCms = {
    hero?: CmsHero | null;
    sections?: Record<string, CmsSection>;
    news?: CmsNewsCard[];
    testimonials?: CmsTestimonial[];
};

type HomeProps = PageProps<{
    cms?: HomeCms;
}>;

function sectionPayload<T extends Record<string, unknown>>(
    sections: Record<string, CmsSection> | undefined,
    key: string,
): T | undefined {
    return sections?.[key]?.payload as T | undefined;
}

export default function Home() {
    const { cms } = usePage<HomeProps>().props;
    const hero = cms?.hero;
    const stats = sectionPayload<{ items?: { value: number; suffix?: string; label: string }[] }>(
        cms?.sections,
        'stats',
    )?.items;
    const teaserNosotros = sectionPayload<{
        eyebrow?: string;
        title?: string;
        description?: string;
        route_name?: string;
        link_label?: string;
    }>(cms?.sections, 'teaser_nosotros');
    const teaserNiveles = sectionPayload<{
        eyebrow?: string;
        title?: string;
        description?: string;
        route_name?: string;
        link_label?: string;
    }>(cms?.sections, 'teaser_niveles');
    const ctaAdmision = sectionPayload<{
        title?: string;
        description?: string;
        primary_route?: string;
        primary_label?: string;
        secondary_route?: string;
        secondary_label?: string;
    }>(cms?.sections, 'cta_admision');
    const ctaContacto = sectionPayload<{
        title?: string;
        description?: string;
        primary_route?: string;
        primary_label?: string;
        secondary_route?: string;
        secondary_label?: string;
    }>(cms?.sections, 'cta_contacto');

    const heroTitle = hero?.title ?? 'Excelencia académica con valores que perduran';
    const heroSubtitle =
        hero?.subtitle ??
        'Colegio privado en Inicial, Primaria y Secundaria. Un campus que inspira, docentes que acompañan y una comunidad que confía.';

    return (
        <PublicLayout
            title="I.E.P. Horizonte — Colegio privado de excelencia"
            description="Institución educativa en Inicial, Primaria y Secundaria."
        >
            <PublicHeroImage
                badge={hero?.badge ?? 'I.E.P. Horizonte · Lima'}
                title={heroTitle}
                subtitle={heroSubtitle}
                imageSrc={hero?.image ?? undefined}
                primaryCta={
                    hero?.primaryCta ?? {
                        label: 'Admisión 2026',
                        href: route('public.admision'),
                    }
                }
                secondaryCta={
                    hero?.secondaryCta ?? {
                        label: 'Conocer niveles',
                        href: route('public.niveles'),
                    }
                }
                statsItems={stats}
            />

            <HomeSectionTeaser
                eyebrow={teaserNosotros?.eyebrow ?? 'Nosotros'}
                title={teaserNosotros?.title ?? 'Una comunidad que aprende y crece junta'}
                description={
                    teaserNosotros?.description ??
                    'Más de quince años formando estudiantes íntegros, competentes y comprometidos con su entorno.'
                }
                href={cmsRoute(teaserNosotros?.route_name, route('public.nosotros'))}
                linkLabel={teaserNosotros?.link_label ?? 'Conocer el colegio'}
            >
                <p className="max-w-3xl text-plomo leading-relaxed dark:text-slate-400">
                    {missionVision.mission.slice(0, 220)}…
                </p>
            </HomeSectionTeaser>

            <HomeSectionTeaser
                eyebrow={teaserNiveles?.eyebrow ?? 'Niveles educativos'}
                title={teaserNiveles?.title ?? 'Tres etapas, una misma excelencia'}
                description={
                    teaserNiveles?.description ??
                    'Progresión curricular alineada al marco nacional con acompañamiento personalizado.'
                }
                href={cmsRoute(teaserNiveles?.route_name, route('public.niveles'))}
                linkLabel={teaserNiveles?.link_label ?? 'Explorar niveles'}
                altBackground
            >
                <div className="grid gap-6 md:grid-cols-3">
                    {nivelesMenu.map((level) => (
                        <LevelCard
                            key={level.key}
                            title={level.label}
                            grades={level.grades}
                            description={level.description}
                            href={navHref(level)}
                            icon={level.icon}
                        />
                    ))}
                </div>
            </HomeSectionTeaser>

            <SchoolLifeEditorialSection />

            <InstitutionalCTA
                title={ctaAdmision?.title ?? 'Admisión 2026 — cupos limitados'}
                description={
                    ctaAdmision?.description ??
                    'Proceso claro, visitas guiadas y acompañamiento para tu familia.'
                }
                primaryLabel={ctaAdmision?.primary_label ?? 'Postular ahora'}
                primaryHref={cmsRoute(ctaAdmision?.primary_route, route('public.admision'))}
                secondaryLabel={ctaAdmision?.secondary_label ?? 'Ver requisitos'}
                secondaryHref={cmsRoute(
                    ctaAdmision?.secondary_route,
                    route('public.admision.requisitos'),
                )}
            />

            <EducationalInnovationSection />

            <CommunityTestimonialsSection items={cms?.testimonials} />

            <NewsMagazineSection compact articles={cms?.news} />

            <InstitutionalCTA
                title={ctaContacto?.title ?? 'Visítanos o escríbenos'}
                description={
                    ctaContacto?.description ??
                    'Av. Institucional 123, San Isidro · admision@horizonte.edu.pe'
                }
                primaryLabel={ctaContacto?.primary_label ?? 'Contacto'}
                primaryHref={cmsRoute(ctaContacto?.primary_route, route('public.contacto'))}
                secondaryLabel={ctaContacto?.secondary_label ?? 'Ver galería'}
                secondaryHref={cmsRoute(ctaContacto?.secondary_route, route('public.galeria'))}
            />
        </PublicLayout>
    );
}
