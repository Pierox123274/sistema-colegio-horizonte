import { schoolLifeActivities } from '@/Components/Public/institutionalSiteData';
import { PageHero } from '@/Components/Public/ui/PageHero';
import { SectionHeader } from '@/Components/Public/ui/SectionHeader';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

export default function Actividades() {
    return (
        <PublicLayout title="Actividades — Vida escolar" description="Proyectos y experiencias.">
            <PageHero
                title="Actividades"
                subtitle="Proyectos interdisciplinarios, ferias y experiencias formativas."
                breadcrumbs={[
                    { label: 'Vida escolar', href: route('public.vida-escolar') },
                    { label: 'Actividades' },
                ]}
            />
            <section className="section-institutional py-16 sm:py-20">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <SectionHeader
                        title="Aprender haciendo"
                        description="Actividades que integran competencias, creatividad y trabajo en equipo."
                    />
                    <div className="mt-10 grid gap-6 sm:grid-cols-2">
                        {schoolLifeActivities.map((item) => (
                            <article
                                key={item.title}
                                className="rounded-2xl border border-slate-200/70 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-slate-900/60"
                            >
                                <h3 className="font-bold text-institutional-blue-900 dark:text-white">
                                    {item.title}
                                </h3>
                                <p className="mt-2 text-sm text-plomo">{item.description}</p>
                            </article>
                        ))}
                    </div>
                </div>
            </section>
            <InstitutionalCTA />
        </PublicLayout>
    );
}
