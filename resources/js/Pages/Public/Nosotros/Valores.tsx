import { missionVision } from '@/Components/Public/institutionalSiteData';
import { PageHero } from '@/Components/Public/ui/PageHero';
import { SectionHeader } from '@/Components/Public/ui/SectionHeader';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

export default function Valores() {
    return (
        <PublicLayout title="Valores — I.E.P. Horizonte" description="Principios institucionales.">
            <PageHero
                title="Valores institucionales"
                subtitle="Principios que orientan la convivencia y la formación integral."
                breadcrumbs={[
                    { label: 'Nosotros', href: route('public.nosotros') },
                    { label: 'Valores' },
                ]}
            />
            <section className="section-institutional py-16 sm:py-20">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <SectionHeader
                        align="center"
                        title="Vivimos nuestros valores cada día"
                        description="Se reflejan en aulas, patios, ceremonias y en el vínculo con las familias."
                    />
                    <ul className="mx-auto mt-12 flex max-w-3xl flex-wrap justify-center gap-3">
                        {missionVision.values.map((v) => (
                            <li
                                key={v}
                                className="rounded-2xl border border-institutional-gold/30 bg-institutional-gold-soft/50 px-6 py-4 text-lg font-bold text-institutional-blue-900 dark:bg-institutional-gold/10 dark:text-white"
                            >
                                {v}
                            </li>
                        ))}
                    </ul>
                </div>
            </section>
            <InstitutionalCTA />
        </PublicLayout>
    );
}
