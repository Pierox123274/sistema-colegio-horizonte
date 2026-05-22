import { Link, usePage } from '@inertiajs/react';
import { BookOpen, Brain, LineChart, Sparkles } from 'lucide-react';
import type { PageProps } from '@/types';
import { Reveal } from '@/Components/Public/Premium/Reveal';
import { innovationItems } from '@/Components/Public/institutionalSiteData';

const icons = [BookOpen, Sparkles, Brain, LineChart];

export function EducationalInnovationSection() {
    const { canLogin = false } = usePage<PageProps>().props;

    return (
        <section
            id="innovacion"
            className="section-institutional-alt section-separator scroll-mt-24 py-16 sm:py-20"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <Reveal className="mx-auto max-w-3xl text-center">
                    <p className="text-xs font-bold uppercase tracking-widest text-institutional-gold">
                        Tecnología educativa
                    </p>
                    <h2 className="mt-2 font-display text-3xl font-bold text-institutional-blue-900 dark:text-white sm:text-4xl">
                        Innovación al servicio del aprendizaje
                    </h2>
                    <p className="mt-4 text-plomo dark:text-slate-400">
                        Recursos digitales que acompañan el aula: sin reemplazar el vínculo humano
                        entre docentes, estudiantes y familias.
                    </p>
                </Reveal>

                <div className="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {innovationItems.map((item, i) => {
                        const Icon = icons[i] ?? Sparkles;
                        return (
                            <Reveal key={item.title} delay={i * 0.06}>
                                <article className="rounded-2xl border border-slate-200/60 bg-white/80 p-5 shadow-sm transition hover:border-institutional-gold/30 hover:shadow-institutional dark:border-white/10 dark:bg-slate-900/50">
                                    <Icon className="h-5 w-5 text-institutional-blue-900 dark:text-institutional-gold" />
                                    <h3 className="mt-3 text-sm font-bold text-institutional-blue-900 dark:text-white">
                                        {item.title}
                                    </h3>
                                    <p className="mt-2 text-xs leading-relaxed text-plomo dark:text-slate-400">
                                        {item.description}
                                    </p>
                                </article>
                            </Reveal>
                        );
                    })}
                </div>

                {canLogin ? (
                    <Reveal className="mt-10 text-center" delay={0.15}>
                        <p className="text-sm text-plomo dark:text-slate-500">
                            Acceso reservado para la comunidad educativa Horizonte.
                        </p>
                        <Link
                            href={route('login')}
                            className="mt-4 inline-flex rounded-2xl border border-institutional-blue-900/20 px-6 py-2.5 text-sm font-semibold text-institutional-blue-900 transition hover:bg-institutional-blue-900 hover:text-white dark:border-institutional-gold/40 dark:text-institutional-gold dark:hover:bg-institutional-gold dark:hover:text-institutional-blue-950"
                        >
                            Portal para familias y docentes
                        </Link>
                    </Reveal>
                ) : null}
            </div>
        </section>
    );
}
