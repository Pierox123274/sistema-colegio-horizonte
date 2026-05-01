import { Link, usePage } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import type { PageProps } from '@/types';

type HeroSectionProps = {
    compact?: boolean;
};

export function HeroSection({ compact = false }: HeroSectionProps) {
    const { canLogin = false } = usePage<PageProps>().props;

    return (
        <section
            className={`relative overflow-hidden bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 ${compact ? 'py-16' : 'py-24 sm:py-32'}`}
        >
            <div
                className="pointer-events-none absolute -right-24 top-0 h-96 w-96 rounded-full bg-brand-red/20 blur-3xl"
                aria-hidden
            />
            <div
                className="pointer-events-none absolute -left-24 bottom-0 h-80 w-80 rounded-full bg-brand-yellow/15 blur-3xl"
                aria-hidden
            />
            <div className="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="max-w-3xl">
                    <p className="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-brand-yellow">
                        Colegio privado de referencia
                    </p>
                    <h1 className="mt-6 text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                        Educar con{' '}
                        <span className="text-brand-yellow">excelencia</span> y
                        valores
                    </h1>
                    <p className="mt-6 text-lg leading-relaxed text-white/80 sm:text-xl">
                        Inicial, Primaria y Secundaria con acompañamiento
                        cercano, innovación pedagógica y ambiente seguro para el
                        desarrollo de cada estudiante.
                    </p>
                    <div className="mt-10 flex flex-wrap gap-4">
                        <Link
                            href={route('public.admision')}
                            className="inline-flex items-center gap-2 rounded-xl bg-brand-yellow px-6 py-3 text-sm font-bold text-navy-950 shadow-lg transition hover:scale-[1.02] hover:brightness-105"
                        >
                            Proceso de admisión
                            <ArrowRight className="h-4 w-4" />
                        </Link>
                        <Link
                            href={route('public.nosotros')}
                            className="inline-flex items-center rounded-xl border border-white/25 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
                        >
                            Conócenos
                        </Link>
                        {canLogin && (
                            <Link
                                href={route('login')}
                                className="inline-flex items-center rounded-xl border border-brand-red/50 bg-brand-red/90 px-6 py-3 text-sm font-semibold text-white transition hover:bg-brand-red"
                            >
                                Acceso intranet
                            </Link>
                        )}
                    </div>
                </div>
            </div>
        </section>
    );
}
