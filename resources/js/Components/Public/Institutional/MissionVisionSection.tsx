import { Link } from '@inertiajs/react';
import { Eye, Flag, Target } from 'lucide-react';
import { Reveal } from '@/Components/Public/Premium/Reveal';
import { missionVision } from '@/Components/Public/institutionalSiteData';

export function MissionVisionSection() {
    return (
        <section
            className="section-institutional-alt section-separator scroll-mt-24 py-16 sm:py-24"
            id="historia"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <Reveal className="mx-auto max-w-3xl text-center">
                    <p className="text-xs font-bold uppercase tracking-widest text-institutional-gold">
                        Nuestra identidad
                    </p>
                    <h2 className="mt-2 font-display text-3xl font-bold text-navy-900 dark:text-white sm:text-4xl">
                        Tradición con propósito, excelencia con experiencia
                    </h2>
                    <p className="mt-4 text-plomo dark:text-slate-400">
                        Una formación integral que combina desarrollo intelectual, valores y
                        proyección al futuro de cada estudiante.
                    </p>
                </Reveal>

                <div className="mt-14 grid gap-6 lg:grid-cols-3" id="mision-vision">
                    <Reveal delay={0.05}>
                        <article className="glass-panel h-full rounded-2xl p-8 scroll-mt-24">
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-red/10 text-brand-red">
                                <Target className="h-6 w-6" />
                            </div>
                            <h3 className="mt-5 text-xl font-bold text-navy-900 dark:text-white">
                                Misión
                            </h3>
                            <p className="mt-3 text-sm leading-relaxed text-plomo dark:text-slate-300">
                                {missionVision.mission}
                            </p>
                        </article>
                    </Reveal>
                    <Reveal delay={0.1}>
                        <article className="glass-panel h-full rounded-2xl p-8">
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-yellow/20 text-navy-900 dark:text-brand-yellow">
                                <Eye className="h-6 w-6" />
                            </div>
                            <h3 className="mt-5 text-xl font-bold text-navy-900 dark:text-white">
                                Visión
                            </h3>
                            <p className="mt-3 text-sm leading-relaxed text-plomo dark:text-slate-300">
                                {missionVision.vision}
                            </p>
                        </article>
                    </Reveal>
                    <Reveal delay={0.15}>
                        <article id="valores" className="glass-panel h-full scroll-mt-24 rounded-2xl p-8">
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-navy-900/10 text-navy-900 dark:bg-white/10 dark:text-white">
                                <Flag className="h-6 w-6" />
                            </div>
                            <h3 className="mt-5 text-xl font-bold text-navy-900 dark:text-white">
                                Valores
                            </h3>
                            <ul className="mt-4 flex flex-wrap gap-2">
                                {missionVision.values.map((v) => (
                                    <li
                                        key={v}
                                        className="rounded-full border border-navy-900/10 bg-white px-3 py-1 text-sm font-semibold text-navy-900 dark:border-white/15 dark:bg-white/5 dark:text-white"
                                    >
                                        {v}
                                    </li>
                                ))}
                            </ul>
                            <Link
                                href={route('public.nosotros')}
                                className="mt-6 inline-block text-sm font-semibold text-brand-red hover:underline"
                            >
                                Nuestra historia completa →
                            </Link>
                        </article>
                    </Reveal>
                </div>
            </div>
        </section>
    );
}
