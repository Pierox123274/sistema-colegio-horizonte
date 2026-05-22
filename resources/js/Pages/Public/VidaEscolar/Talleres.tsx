import { PageHero } from '@/Components/Public/ui/PageHero';
import { SectionHeader } from '@/Components/Public/ui/SectionHeader';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

const talleres = [
    'Club de robótica y programación',
    'Oratoria y debate',
    'Artes plásticas y muralismo',
    'Inglés conversacional',
    'Emprendimiento juvenil',
];

export default function Talleres() {
    return (
        <PublicLayout title="Talleres — Vida escolar" description="Clubes formativos.">
            <PageHero
                title="Talleres formativos"
                subtitle="Espacios extracurriculares para explorar talentos e intereses."
                breadcrumbs={[
                    { label: 'Vida escolar', href: route('public.vida-escolar') },
                    { label: 'Talleres' },
                ]}
            />
            <section className="section-institutional py-16 sm:py-20">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <SectionHeader title="Oferta de talleres (demo)" />
                    <ul className="mt-8 space-y-3">
                        {talleres.map((t) => (
                            <li
                                key={t}
                                className="rounded-xl border border-slate-200/70 bg-institutional-surface-alt px-5 py-4 font-medium text-institutional-blue-900 dark:text-white"
                            >
                                {t}
                            </li>
                        ))}
                    </ul>
                </div>
            </section>
            <InstitutionalCTA />
        </PublicLayout>
    );
}
