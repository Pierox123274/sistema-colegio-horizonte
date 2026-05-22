import { Quote } from 'lucide-react';
import { Reveal } from './Reveal';
import { testimonials } from './publicSiteData';

export function TestimonialsSection() {
    return (
        <section id="testimonios" className="scroll-mt-24 py-20 sm:py-28">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <Reveal className="text-center">
                    <h2 className="font-display text-3xl font-bold text-slate-900 dark:text-white sm:text-4xl">
                        Confianza institucional
                    </h2>
                    <p className="mt-4 text-slate-600 dark:text-slate-400">
                        Voces de la comunidad educativa (contenido demostrativo).
                    </p>
                </Reveal>

                <div className="mt-14 grid gap-8 md:grid-cols-3">
                    {testimonials.map((t, i) => (
                        <Reveal key={t.name} delay={i * 0.1}>
                            <article className="glass-panel relative flex h-full flex-col rounded-2xl p-8">
                                <Quote className="h-8 w-8 text-brand-yellow/80" />
                                <p className="mt-4 flex-1 text-sm leading-relaxed text-slate-700 dark:text-slate-300">
                                    “{t.quote}”
                                </p>
                                <div className="mt-6 border-t border-slate-200/80 pt-6 dark:border-white/10">
                                    <p className="font-bold text-slate-900 dark:text-white">{t.name}</p>
                                    <p className="text-sm text-slate-500 dark:text-slate-400">
                                        {t.role} · {t.org}
                                    </p>
                                </div>
                            </article>
                        </Reveal>
                    ))}
                </div>
            </div>
        </section>
    );
}
