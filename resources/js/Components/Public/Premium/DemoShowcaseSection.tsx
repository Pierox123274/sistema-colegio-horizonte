import { Link } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import { Reveal } from './Reveal';
import { demoPanels } from './publicSiteData';

export function DemoShowcaseSection() {
    return (
        <section
            id="demo"
            className="scroll-mt-24 bg-gradient-to-b from-slate-50 to-white py-20 dark:from-slate-950 dark:to-slate-900 sm:py-28"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <Reveal className="text-center">
                    <p className="text-sm font-bold uppercase tracking-widest text-brand-red">
                        Explora la plataforma
                    </p>
                    <h2 className="mt-3 font-display text-3xl font-bold text-slate-900 dark:text-white sm:text-4xl">
                        Paneles reales, experiencia premium
                    </h2>
                    <p className="mx-auto mt-4 max-w-2xl text-slate-600 dark:text-slate-400">
                        Docentes, estudiantes y administración acceden a interfaces modernas
                        integradas con el mismo núcleo académico.
                    </p>
                </Reveal>

                <div className="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    {demoPanels.map((panel, i) => {
                        const Icon = panel.icon;
                        return (
                            <Reveal key={panel.title} delay={i * 0.08}>
                                <div className="group h-full rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-brand-yellow/40 hover:shadow-premium dark:border-white/10 dark:bg-slate-900/60">
                                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-navy-900 text-brand-yellow transition group-hover:scale-105">
                                        <Icon className="h-5 w-5" />
                                    </div>
                                    <h3 className="mt-4 font-bold text-slate-900 dark:text-white">
                                        {panel.title}
                                    </h3>
                                    <p className="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                                        {panel.desc}
                                    </p>
                                    <div className="mt-4 h-1 w-12 rounded-full bg-gradient-to-r from-brand-yellow to-brand-red opacity-80" />
                                </div>
                            </Reveal>
                        );
                    })}
                </div>

                <Reveal className="mt-12 text-center" delay={0.2}>
                    <Link
                        href={route('login')}
                        className="inline-flex items-center gap-2 rounded-2xl bg-brand-red px-8 py-4 text-sm font-bold text-white shadow-lg transition hover:-translate-y-0.5 hover:brightness-110"
                    >
                        Acceder a la demostración
                        <ArrowRight className="h-4 w-4" />
                    </Link>
                </Reveal>
            </div>
        </section>
    );
}
