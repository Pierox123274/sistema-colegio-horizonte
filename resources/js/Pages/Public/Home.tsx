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

export default function Home() {
    return (
        <PublicLayout
            title="I.E.P. Horizonte — Colegio privado de excelencia"
            description="Institución educativa en Inicial, Primaria y Secundaria. Admisión, vida escolar y excelencia académica."
        >
            <PublicHeroImage
                title={
                    <>
                        Excelencia académica con{' '}
                        <span className="text-amber-400">valores que perduran</span>
                    </>
                }
                subtitle="Colegio privado en Inicial, Primaria y Secundaria. Un campus que inspira, docentes que acompañan y una comunidad que confía."
                primaryCta={{ label: 'Admisión 2026', href: route('public.admision') }}
                secondaryCta={{ label: 'Conocer niveles', href: route('public.niveles') }}
            />

            <HomeSectionTeaser
                eyebrow="Nosotros"
                title="Una comunidad que aprende y crece junta"
                description="Más de quince años formando estudiantes íntegros, competentes y comprometidos con su entorno."
                href={route('public.nosotros')}
                linkLabel="Conocer el colegio"
            >
                <p className="max-w-3xl text-plomo leading-relaxed dark:text-slate-400">
                    {missionVision.mission.slice(0, 220)}…
                </p>
            </HomeSectionTeaser>

            <HomeSectionTeaser
                eyebrow="Niveles educativos"
                title="Tres etapas, una misma excelencia"
                description="Progresión curricular alineada al marco nacional con acompañamiento personalizado."
                href={route('public.niveles')}
                linkLabel="Explorar niveles"
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
                title="Admisión 2026 — cupos limitados"
                description="Proceso claro, visitas guiadas y acompañamiento para tu familia."
                primaryLabel="Postular ahora"
                primaryHref={route('public.admision')}
                secondaryLabel="Ver requisitos"
                secondaryHref={route('public.admision.requisitos')}
            />

            <EducationalInnovationSection />

            <CommunityTestimonialsSection />

            <NewsMagazineSection compact />

            <InstitutionalCTA
                title="Visítanos o escríbenos"
                description="Av. Institucional 123, San Isidro · admision@horizonte.edu.pe"
                primaryLabel="Contacto"
                primaryHref={route('public.contacto')}
                secondaryLabel="Ver galería"
                secondaryHref={route('public.galeria')}
            />
        </PublicLayout>
    );
}
