import { Link, usePage } from '@inertiajs/react';
import type { PageProps } from '@/types';
import { ArrowRight } from 'lucide-react';

export function CTASection() {
    const { canLogin = false } = usePage<PageProps>().props;

    return (
        <section className="relative overflow-hidden bg-brand-red py-16 sm:py-20">
            <div
                className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.15),transparent_50%)]"
                aria-hidden
            />
            <div className="relative mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <h2 className="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                    ¿Lista tu familia para ser parte de Horizonte?
                </h2>
                <p className="mx-auto mt-4 max-w-2xl text-base text-white/90">
                    Agenda una visita o solicita información sin compromiso.
                    Nuestro equipo de admisiones responderá a la brevedad
                    (flujos reales en fases posteriores).
                </p>
                <div className="mt-10 flex flex-wrap justify-center gap-4">
                    <Link
                        href={route('public.contacto')}
                        className="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-bold text-brand-red shadow-lg transition hover:scale-[1.02]"
                    >
                        Contactar
                        <ArrowRight className="h-4 w-4" />
                    </Link>
                    <Link
                        href={route('public.admision')}
                        className="inline-flex items-center rounded-xl border-2 border-white/80 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
                    >
                        Requisitos de admisión
                    </Link>
                    {canLogin && (
                        <Link
                            href={route('login')}
                            className="inline-flex items-center rounded-xl border-2 border-white/40 px-6 py-3 text-sm font-semibold text-white/95 transition hover:bg-white/10"
                        >
                            Portal intranet
                        </Link>
                    )}
                </div>
            </div>
        </section>
    );
}
