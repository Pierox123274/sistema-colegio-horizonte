import { Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowRight, Play, Sparkles } from 'lucide-react';
import type { PageProps } from '@/types';
import { AnimatedCounter } from './AnimatedCounter';
import { DashboardMockup } from './DashboardMockup';
import { heroStats, techBadges } from './publicSiteData';

export function PremiumHero() {
    const { canLogin = false } = usePage<PageProps>().props;

    return (
        <section
            id="inicio"
            className="relative overflow-hidden bg-slate-50 bg-mesh-light pb-20 pt-28 dark:bg-slate-950 dark:bg-mesh-dark sm:pb-28 sm:pt-32"
        >
            <motion.div
                className="pointer-events-none absolute left-1/2 top-0 h-[480px] w-[720px] -translate-x-1/2 rounded-full bg-gradient-to-r from-brand-yellow/20 via-brand-red/10 to-violet-500/10 blur-3xl"
                animate={{ opacity: [0.5, 0.8, 0.5], scale: [1, 1.05, 1] }}
                transition={{ duration: 8, repeat: Infinity, ease: 'easeInOut' }}
            />

            <div className="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
                    <div>
                        <motion.div
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.5 }}
                            className="flex flex-wrap gap-2"
                        >
                            {techBadges.slice(0, 4).map((badge) => (
                                <span
                                    key={badge}
                                    className="inline-flex items-center gap-1 rounded-full border border-navy-900/10 bg-white/70 px-3 py-1 text-xs font-semibold text-navy-900 backdrop-blur dark:border-white/10 dark:bg-white/5 dark:text-white/90"
                                >
                                    <Sparkles className="h-3 w-3 text-brand-yellow" />
                                    {badge}
                                </span>
                            ))}
                        </motion.div>

                        <motion.h1
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.55, delay: 0.08 }}
                            className="mt-6 font-display text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-5xl lg:text-[3.25rem] lg:leading-[1.1]"
                        >
                            Transformamos la educación con{' '}
                            <span className="text-gradient-brand">inteligencia académica</span> y
                            aprendizaje adaptativo
                        </motion.h1>

                        <motion.p
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.55, delay: 0.16 }}
                            className="mt-6 max-w-xl text-lg leading-relaxed text-slate-600 dark:text-slate-300"
                        >
                            Plataforma enterprise para I.E.P. Horizonte: ERP, aula virtual LMS, tutor
                            IA, diagnósticos inteligentes y analítica institucional en una experiencia
                            moderna y segura.
                        </motion.p>

                        <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.55, delay: 0.24 }}
                            className="mt-10 flex flex-wrap gap-3"
                        >
                            <Link
                                href={route('public.home') + '#demo'}
                                className="inline-flex items-center gap-2 rounded-2xl bg-navy-900 px-6 py-3.5 text-sm font-bold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-navy-950 hover:shadow-premium dark:bg-brand-yellow dark:text-navy-950 dark:hover:brightness-110"
                            >
                                Ver plataforma
                                <ArrowRight className="h-4 w-4" />
                            </Link>
                            <Link
                                href={route('public.contacto')}
                                className="inline-flex items-center gap-2 rounded-2xl border border-slate-300 bg-white/80 px-6 py-3.5 text-sm font-semibold text-navy-900 backdrop-blur transition hover:border-brand-yellow hover:bg-white dark:border-white/15 dark:bg-white/5 dark:text-white dark:hover:border-brand-yellow/50"
                            >
                                <Play className="h-4 w-4 text-brand-red" />
                                Solicitar demo
                            </Link>
                            {canLogin && (
                                <Link
                                    href={route('login')}
                                    className="inline-flex items-center rounded-2xl px-5 py-3.5 text-sm font-semibold text-slate-600 transition hover:text-navy-900 dark:text-slate-300 dark:hover:text-white"
                                >
                                    Iniciar sesión
                                </Link>
                            )}
                        </motion.div>

                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            transition={{ delay: 0.4 }}
                            className="mt-12 grid grid-cols-2 gap-6 sm:grid-cols-4"
                        >
                            {heroStats.map((stat, i) => (
                                <div key={stat.label}>
                                    <p className="text-2xl font-bold text-navy-900 dark:text-white sm:text-3xl">
                                        <AnimatedCounter value={stat.value} suffix={stat.suffix} />
                                    </p>
                                    <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        {stat.label}
                                    </p>
                                </div>
                            ))}
                        </motion.div>
                    </div>

                    <DashboardMockup />
                </div>
            </div>
        </section>
    );
}
