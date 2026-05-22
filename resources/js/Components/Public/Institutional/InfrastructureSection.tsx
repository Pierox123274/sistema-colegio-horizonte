import { Building2 } from 'lucide-react';
import { Reveal } from '@/Components/Public/Premium/Reveal';
import { infrastructureHighlights } from '@/Components/Public/institutionalSiteData';

export function InfrastructureSection() {
    return (
        <section
            id="infraestructura"
            className="section-institutional section-separator scroll-mt-24 py-16 sm:py-24"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <Reveal className="mx-auto max-w-2xl text-center">
                    <p className="text-xs font-bold uppercase tracking-widest text-brand-red">
                        Infraestructura
                    </p>
                    <h2 className="mt-2 font-display text-3xl font-bold text-navy-900 dark:text-white sm:text-4xl">
                        Campus pensado para aprender
                    </h2>
                    <p className="mt-4 text-plomo dark:text-slate-400">
                        Espacios seguros, modernos y acogedores que favorecen la concentración,
                        el juego y la convivencia sana.
                    </p>
                </Reveal>
                <div className="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    {infrastructureHighlights.map((item, i) => (
                        <Reveal key={item.title} delay={i * 0.07}>
                            <article className="group overflow-hidden rounded-2xl border border-plomo/10 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-white/10 dark:bg-slate-900">
                                <div className="aspect-[4/3] bg-gradient-to-br from-navy-100 to-brand-yellow/10 dark:from-slate-800 dark:to-navy-900/40" />
                                <div className="p-5">
                                    <Building2 className="h-5 w-5 text-brand-red" />
                                    <h3 className="mt-2 font-bold text-navy-900 dark:text-white">
                                        {item.title}
                                    </h3>
                                    <p className="mt-1 text-sm text-plomo dark:text-slate-400">
                                        {item.detail}
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
