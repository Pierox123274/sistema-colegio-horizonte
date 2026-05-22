import { AdmissionPremiumSection } from '@/Components/Public/Premium/AdmissionPremiumSection';
import { PublicPageHeroImage } from '@/Components/Public/Premium/PublicHeroImage';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

export default function AdmisionIndex() {
    return (
        <PublicLayout title="Admisión 2026 — I.E.P. Horizonte" description="Proceso de ingreso.">
            <PublicPageHeroImage
                title="Admisión 2026"
                subtitle="Un proceso transparente, humano y orientado a la familia."
                imageKey="heroAdmision"
                breadcrumbs={[{ label: 'Admisión' }]}
            />
            <AdmissionPremiumSection />
            <section className="section-institutional py-16">
                <div className="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
                    <h2 className="font-display text-2xl font-bold text-slate-900 dark:text-white">
                        Contacto rápido
                    </h2>
                    <p className="mt-4 text-slate-600 dark:text-slate-300">
                        Escríbenos a{' '}
                        <a
                            href="mailto:admision@horizonte.edu.pe"
                            className="font-semibold text-amber-600 hover:underline dark:text-amber-400"
                        >
                            admision@horizonte.edu.pe
                        </a>{' '}
                        o completa el formulario en la página de contacto.
                    </p>
                </div>
            </section>
            <InstitutionalCTA
                primaryLabel="Ir a contacto"
                primaryHref={route('public.contacto')}
            />
        </PublicLayout>
    );
}
