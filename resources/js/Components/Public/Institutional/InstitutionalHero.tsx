import { motion } from 'framer-motion';
import { ArrowRight, Compass, Sparkles } from 'lucide-react';
import { InstitutionalButtonLink } from '@/Components/Public/ui/InstitutionalButton';

export function InstitutionalHero() {
    return (
        <section
            id="inicio"
            className="relative min-h-[92vh] overflow-hidden bg-institutional-blue-950 text-white dark:bg-slate-950"
        >
            <div
                className="absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_50%_-10%,rgba(201,162,39,0.18),transparent_55%)]"
                aria-hidden
            />
            <div
                className="absolute inset-0 bg-[radial-gradient(circle_at_85%_70%,rgba(59,130,246,0.12),transparent_45%)]"
                aria-hidden
            />
            <div
                className="absolute inset-0 bg-gradient-to-t from-institutional-blue-950 via-institutional-blue-950/40 to-transparent"
                aria-hidden
            />
            <div
                className="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.02\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-80"
                aria-hidden
            />

            <motion.div
                className="pointer-events-none absolute -right-24 top-16 h-[28rem] w-[28rem] rounded-full bg-institutional-gold/12 blur-3xl"
                animate={{ opacity: [0.5, 0.85, 0.5], scale: [1, 1.05, 1] }}
                transition={{ duration: 12, repeat: Infinity, ease: 'easeInOut' }}
            />
            <motion.div
                className="pointer-events-none absolute -left-16 bottom-0 h-80 w-80 rounded-full bg-institutional-accent/10 blur-3xl"
                animate={{ opacity: [0.4, 0.7, 0.4] }}
                transition={{ duration: 14, repeat: Infinity, ease: 'easeInOut' }}
            />

            <div className="relative mx-auto max-w-7xl px-4 pb-20 pt-32 sm:px-6 sm:pb-24 sm:pt-36 lg:px-8 lg:pt-40">
                <div className="grid items-center gap-14 lg:grid-cols-2 lg:gap-20">
                    <div>
                        <motion.p
                            initial={{ opacity: 0, y: 12 }}
                            animate={{ opacity: 1, y: 0 }}
                            className="inline-flex items-center gap-2 rounded-full border border-white/12 bg-white/5 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-institutional-gold-light backdrop-blur-md"
                        >
                            <Sparkles className="h-3.5 w-3.5" />
                            I.E.P. Horizonte · Lima
                        </motion.p>
                        <motion.h1
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ delay: 0.1, duration: 0.8 }}
                            className="mt-8 font-display text-4xl font-extrabold leading-[1.08] tracking-tight sm:text-5xl lg:text-[3.35rem]"
                        >
                            Formamos líderes con{' '}
                            <span className="text-gradient-brand">excelencia</span> y propósito
                        </motion.h1>
                        <motion.p
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ delay: 0.2, duration: 0.7 }}
                            className="mt-6 max-w-xl text-lg leading-relaxed text-white/78"
                        >
                            Colegio privado de referencia en Inicial, Primaria y Secundaria.
                            Rigor académico, valores sólidos y una comunidad que inspira confianza
                            a cada familia.
                        </motion.p>
                        <motion.div
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ delay: 0.32 }}
                            className="mt-10 flex flex-wrap gap-4"
                        >
                            <InstitutionalButtonLink
                                href={route('public.admision')}
                                variant="primary"
                            >
                                Admisión 2026
                                <ArrowRight className="h-4 w-4" />
                            </InstitutionalButtonLink>
                            <InstitutionalButtonLink
                                href={route('public.niveles')}
                                variant="secondary"
                            >
                                Explorar niveles
                            </InstitutionalButtonLink>
                        </motion.div>
                    </div>

                    <motion.div
                        initial={{ opacity: 0, scale: 0.96, y: 24 }}
                        animate={{ opacity: 1, scale: 1, y: 0 }}
                        transition={{ delay: 0.25, duration: 1 }}
                        className="relative"
                    >
                        <div className="absolute -inset-4 rounded-[2rem] bg-gradient-to-br from-institutional-gold/20 via-transparent to-institutional-accent/15 blur-2xl" />
                        <div className="relative aspect-[5/4] overflow-hidden rounded-[1.75rem] border border-white/10 shadow-institutional-lg ring-1 ring-white/10 sm:aspect-[4/3]">
                            <div className="absolute inset-0 bg-gradient-to-br from-institutional-blue-900/90 via-institutional-blue-800/80 to-institutional-blue-950/95" />
                            <motion.div
                                className="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(228,199,106,0.35),transparent_50%)]"
                                animate={{ opacity: [0.6, 1, 0.6] }}
                                transition={{ duration: 8, repeat: Infinity }}
                            />
                            <div className="relative flex h-full flex-col justify-end p-8 sm:p-10">
                                <p className="text-xs font-bold uppercase tracking-[0.2em] text-institutional-gold-light">
                                    Vida escolar · Campus
                                </p>
                                <p className="mt-3 max-w-sm font-display text-2xl font-bold leading-snug sm:text-3xl">
                                    Un entorno seguro donde crecen con propósito
                                </p>
                                <p className="mt-3 text-sm text-white/72">
                                    Estudiantes, docentes y familias en comunidad.
                                </p>
                            </div>
                        </div>
                        <motion.div
                            initial={{ opacity: 0, x: -12 }}
                            animate={{ opacity: 1, x: 0 }}
                            transition={{ delay: 0.5 }}
                            className="absolute -bottom-6 -left-4 hidden rounded-2xl border border-institutional-gold/25 bg-institutional-blue-900/95 px-6 py-4 shadow-xl backdrop-blur-md sm:block"
                        >
                            <p className="flex items-center gap-2 text-2xl font-bold text-institutional-gold">
                                <Compass className="h-6 w-6" />
                                15+
                            </p>
                            <p className="text-xs font-medium text-white/75">Años de trayectoria</p>
                        </motion.div>
                        <InstitutionalButtonLink
                            href={route('login')}
                            variant="ghost"
                            className="absolute -right-2 top-4 hidden border-white/20 bg-white/10 text-white backdrop-blur-md hover:bg-white/15 sm:inline-flex"
                        >
                            Portal institucional
                        </InstitutionalButtonLink>
                    </motion.div>
                </div>
            </div>
        </section>
    );
}
