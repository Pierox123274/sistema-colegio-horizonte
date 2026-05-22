import { admissionTimeline } from '@/Components/Public/nav/publicNavConfig';
import { PageHero } from '@/Components/Public/ui/PageHero';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

export default function AdmisionMatricula() {
    return (
        <PublicLayout title="Matrícula 2026 — I.E.P. Horizonte" description="Confirmación de vacante.">
            <PageHero
                title="Matrícula 2026"
                subtitle="Confirma la vacante y completa el proceso de bienvenida."
                breadcrumbs={[
                    { label: 'Admisión', href: route('public.admision') },
                    { label: 'Matrícula' },
                ]}
            />
            <section className="section-institutional py-16 sm:py-20">
                <div className="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                    <ul className="relative">
                        {admissionTimeline.map((step, i) => (
                            <li key={step.title} className="relative flex gap-4 pb-8 last:pb-0">
                                {i < admissionTimeline.length - 1 ? (
                                    <span
                                        className="absolute left-[1.1rem] top-9 h-[calc(100%-1rem)] w-px bg-institutional-gold/50"
                                        aria-hidden
                                    />
                                ) : null}
                                <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-institutional-blue-900 text-xs font-bold text-institutional-gold">
                                    {step.date}
                                </span>
                                <div>
                                    <p className="font-semibold text-institutional-blue-900 dark:text-white">
                                        {step.title}
                                    </p>
                                    <p className="text-sm text-plomo">{step.desc}</p>
                                </div>
                            </li>
                        ))}
                    </ul>
                    <p className="mt-8 text-sm text-plomo">
                        La matrícula en línea estará disponible para familias a través del portal institucional
                        (demo).
                    </p>
                </div>
            </section>
            <InstitutionalCTA primaryLabel="Solicitar información" primaryHref={route('public.contacto')} />
        </PublicLayout>
    );
}
