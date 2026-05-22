import { Reveal } from '@/Components/Public/Premium/Reveal';
import { AnimatedCounter } from '@/Components/Public/Premium/AnimatedCounter';
import { institutionStats } from '@/Components/Public/institutionalSiteData';

export function InstitutionStatsBar() {
    return (
        <section className="border-y border-slate-200/60 bg-institutional-surface-alt py-12 dark:border-white/10 dark:bg-slate-900/60">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="grid grid-cols-2 gap-8 lg:grid-cols-4">
                    {institutionStats.map((stat, i) => (
                        <Reveal key={stat.label} delay={i * 0.06} className="text-center">
                            <p className="text-3xl font-extrabold text-navy-900 dark:text-brand-yellow sm:text-4xl">
                                <AnimatedCounter value={stat.value} suffix={stat.suffix} />
                            </p>
                            <p className="mt-2 text-sm font-medium text-plomo dark:text-slate-400">
                                {stat.label}
                            </p>
                        </Reveal>
                    ))}
                </div>
            </div>
        </section>
    );
}
