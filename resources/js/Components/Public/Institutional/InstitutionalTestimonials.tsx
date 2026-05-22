import { Quote } from 'lucide-react';
import { Reveal } from '@/Components/Public/Premium/Reveal';
import { institutionalTestimonials } from '@/Components/Public/institutionalSiteData';

export function InstitutionalTestimonials() {
    return (
        <section id="testimonios" className="scroll-mt-24 bg-navy-50 py-16 dark:bg-slate-900/50 sm:py-24">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <Reveal className="text-center">
                    <p className="text-xs font-bold uppercase tracking-widest text-brand-red">
                        Comunidad educativa
                    </p>
                    <h2 className="mt-2 font-display text-3xl font-bold text-navy-900 dark:text-white sm:text-4xl">
                        Voces que nos inspiran
                    </h2>
                </Reveal>
                <div className="mt-12 grid gap-6 md:grid-cols-3">
                    {institutionalTestimonials.map((t, i) => (
                        <Reveal key={t.name} delay={i * 0.08}>
                            <article className="flex h-full flex-col rounded-2xl border border-plomo/10 bg-white p-8 shadow-sm dark:border-white/10 dark:bg-slate-900">
                                <Quote className="h-7 w-7 text-brand-yellow" />
                                <p className="mt-4 flex-1 text-sm leading-relaxed text-plomo dark:text-slate-300">
                                    “{t.quote}”
                                </p>
                                <div className="mt-6 border-t border-plomo/10 pt-4 dark:border-white/10">
                                    <p className="font-bold text-navy-900 dark:text-white">{t.name}</p>
                                    <p className="text-xs text-plomo dark:text-slate-500">
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
