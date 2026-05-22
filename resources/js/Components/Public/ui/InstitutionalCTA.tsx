import { Link } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';

type InstitutionalCTAProps = {
    title?: string;
    description?: string;
    primaryLabel?: string;
    primaryHref?: string;
    secondaryLabel?: string;
    secondaryHref?: string;
};

export function InstitutionalCTA({
    title = '¿Listo para conocer Horizonte?',
    description = 'Agenda una visita guiada o solicita información sobre admisión 2026.',
    primaryLabel = 'Postular ahora',
    primaryHref,
    secondaryLabel = 'Contacto',
    secondaryHref,
}: InstitutionalCTAProps) {
    const primary = primaryHref ?? route('public.admision');
    const secondary = secondaryHref ?? route('public.contacto');

    return (
        <section className="section-institutional-alt py-14 sm:py-16">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="overflow-hidden rounded-3xl border border-slate-200/70 bg-gradient-to-br from-institutional-blue-900 to-institutional-blue-800 p-8 text-white shadow-institutional-lg sm:p-12 dark:border-white/10">
                    <div className="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 className="font-display text-2xl font-bold sm:text-3xl">{title}</h2>
                            <p className="mt-3 max-w-xl text-white/78">{description}</p>
                        </div>
                        <div className="flex flex-wrap gap-3">
                            <Link
                                href={primary}
                                className="inline-flex items-center gap-2 rounded-2xl bg-institutional-gold px-6 py-3.5 text-sm font-bold text-institutional-blue-950 transition hover:bg-institutional-gold-light"
                            >
                                {primaryLabel}
                                <ArrowRight className="h-4 w-4" />
                            </Link>
                            <Link
                                href={secondary}
                                className="inline-flex items-center rounded-2xl border border-white/30 px-6 py-3.5 text-sm font-semibold transition hover:bg-white/10"
                            >
                                {secondaryLabel}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
