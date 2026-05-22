import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowRight, Rocket } from 'lucide-react';

export function FinalCTASection() {
    return (
        <section className="pb-24 pt-8">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <motion.div
                    className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-navy-900 via-navy-950 to-slate-950 px-8 py-16 text-center shadow-premium sm:px-16"
                    initial={{ opacity: 0, scale: 0.98 }}
                    whileInView={{ opacity: 1, scale: 1 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.6 }}
                >
                    <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(228,188,15,0.2),transparent_50%)]" />
                    <Rocket className="mx-auto h-10 w-10 text-brand-yellow" />
                    <h2 className="relative mt-6 font-display text-3xl font-bold text-white sm:text-4xl">
                        Modernice su institución educativa hoy
                    </h2>
                    <p className="relative mx-auto mt-4 max-w-2xl text-lg text-white/75">
                        Transforme el aprendizaje con tecnología real: ERP, LMS, IA y analítica en una
                        sola plataforma enterprise.
                    </p>
                    <div className="relative mt-10 flex flex-wrap items-center justify-center gap-4">
                        <Link
                            href={route('public.contacto')}
                            className="inline-flex items-center gap-2 rounded-2xl bg-brand-yellow px-8 py-4 text-sm font-bold text-navy-950 transition hover:brightness-110"
                        >
                            Solicitar demo
                            <ArrowRight className="h-4 w-4" />
                        </Link>
                        <Link
                            href={route('public.admision')}
                            className="inline-flex items-center rounded-2xl border border-white/25 px-8 py-4 text-sm font-semibold text-white transition hover:bg-white/10"
                        >
                            Proceso de admisión
                        </Link>
                    </div>
                </motion.div>
            </div>
        </section>
    );
}
