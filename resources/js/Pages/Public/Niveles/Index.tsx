import { nivelesMenu, navHref } from '@/Components/Public/nav/publicNavConfig';
import { PageHero } from '@/Components/Public/ui/PageHero';
import { LevelCard } from '@/Components/Public/ui/LevelCard';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

export default function NivelesIndex() {
    return (
        <PublicLayout title="Niveles educativos — I.E.P. Horizonte" description="Inicial, Primaria y Secundaria.">
            <PageHero
                title="Niveles educativos"
                subtitle="Una progresión curricular coherente con excelencia en cada etapa."
                breadcrumbs={[{ label: 'Niveles' }]}
            />
            <section className="section-institutional py-16 sm:py-20">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="grid gap-6 md:grid-cols-3">
                        {nivelesMenu.map((level) => (
                            <LevelCard
                                key={level.key}
                                title={level.label}
                                grades={level.grades}
                                description={level.description}
                                href={navHref(level)}
                                icon={level.icon}
                                accent={level.color}
                            />
                        ))}
                    </div>
                </div>
            </section>
            <InstitutionalCTA primaryLabel="Postular ahora" />
        </PublicLayout>
    );
}
