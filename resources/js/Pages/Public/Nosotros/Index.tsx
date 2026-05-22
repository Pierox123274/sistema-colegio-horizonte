import { AboutSection } from '@/Components/Public/AboutSection';
import { leadershipTeam } from '@/Components/Public/data/publicSiteContent';
import { PageHero } from '@/Components/Public/ui/PageHero';
import { SectionHeader } from '@/Components/Public/ui/SectionHeader';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

export default function NosotrosIndex() {
    return (
        <PublicLayout
            title="Nosotros — I.E.P. Horizonte"
            description="Presentación institucional, comunidad y propuesta educativa."
        >
            <PageHero
                title="Presentación institucional"
                subtitle="Un colegio privado con identidad, excelencia académica y calidez humana."
                breadcrumbs={[{ label: 'Nosotros' }]}
            />
            <AboutSection />
            <section className="section-institutional-alt py-16 sm:py-20">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <SectionHeader
                        eyebrow="Liderazgo"
                        title="Equipo directivo"
                        description="Profesionales comprometidos con la calidad y el bienestar de la comunidad."
                    />
                    <div className="mt-10 grid gap-4 sm:grid-cols-3">
                        {leadershipTeam.map((person) => (
                            <article
                                key={person.name}
                                className="rounded-2xl border border-slate-200/70 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-slate-900/60"
                            >
                                <h3 className="font-bold text-institutional-blue-900 dark:text-white">
                                    {person.name}
                                </h3>
                                <p className="text-sm font-semibold text-institutional-gold">{person.role}</p>
                                <p className="mt-2 text-sm text-plomo">{person.area}</p>
                            </article>
                        ))}
                    </div>
                </div>
            </section>
            <InstitutionalCTA />
        </PublicLayout>
    );
}
