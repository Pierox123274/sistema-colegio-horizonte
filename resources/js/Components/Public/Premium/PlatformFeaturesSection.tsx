import { lazy, Suspense } from 'react';
import { Check } from 'lucide-react';
import { Reveal } from './Reveal';
import { featureModules, type FeatureModule } from './publicSiteData';

const MiniAnalyticsChart = lazy(() =>
    import('./MiniAnalyticsChart').then((m) => ({ default: m.MiniAnalyticsChart })),
);

const accentMap: Record<FeatureModule['accent'], string> = {
    navy: 'from-navy-900/10 to-navy-900/5 border-navy-900/15 dark:from-navy-900/40',
    violet: 'from-violet-500/10 to-violet-500/5 border-violet-500/20',
    amber: 'from-amber-400/15 to-brand-yellow/5 border-brand-yellow/25',
    emerald: 'from-emerald-500/10 to-emerald-500/5 border-emerald-500/20',
    sky: 'from-sky-500/10 to-sky-500/5 border-sky-500/20',
};

function FeatureVisual({ module }: { module: FeatureModule }) {
    const Icon = module.icon;

    return (
        <div
            className={`glass-panel flex h-full min-h-[280px] flex-col justify-between rounded-2xl bg-gradient-to-br p-6 ${accentMap[module.accent]}`}
        >
            <div>
                <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-white shadow-sm dark:bg-white/10">
                    <Icon className="h-6 w-6 text-navy-900 dark:text-brand-yellow" />
                </div>
                <div className="mt-6 space-y-2">
                    {module.highlights.slice(0, 3).map((h) => (
                        <div
                            key={h}
                            className="flex items-center gap-2 rounded-lg bg-white/60 px-3 py-2 text-sm dark:bg-white/5"
                        >
                            <span className="h-2 w-2 rounded-full bg-brand-yellow" />
                            <span className="text-slate-700 dark:text-slate-200">{h}</span>
                        </div>
                    ))}
                </div>
            </div>
            {module.id === 'analitica' && (
                <Suspense fallback={<div className="mt-4 h-24 animate-pulse rounded-lg bg-white/50" />}>
                    <MiniAnalyticsChart variant="bar" />
                </Suspense>
            )}
            {module.id === 'lms' && (
                <div className="mt-4 grid grid-cols-3 gap-2">
                    {['Tarea', 'Examen', 'Recurso'].map((t) => (
                        <div
                            key={t}
                            className="rounded-lg bg-navy-900/90 py-2 text-center text-[10px] font-semibold text-white"
                        >
                            {t}
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

export function PlatformFeaturesSection() {
    return (
        <section
            id="funcionalidades"
            className="scroll-mt-24 bg-white py-20 dark:bg-slate-900/50 sm:py-28"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <Reveal className="mx-auto max-w-3xl text-center">
                    <p className="text-sm font-bold uppercase tracking-widest text-brand-red">
                        Ecosistema completo
                    </p>
                    <h2 className="mt-3 font-display text-3xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                        Una plataforma, todos los módulos
                    </h2>
                    <p className="mt-4 text-lg text-slate-600 dark:text-slate-400">
                        ERP académico, LMS, IA, diagnóstico adaptativo y analítica — diseñados para
                        instituciones que exigen excelencia operativa y pedagógica.
                    </p>
                </Reveal>

                <div className="mt-16 space-y-24">
                    {featureModules.map((module, index) => {
                        const sectionId =
                            module.id === 'ia-educativa'
                                ? 'ia-educativa'
                                : module.id === 'seguridad'
                                  ? 'seguridad'
                                  : module.id === 'analitica'
                                    ? 'analitica'
                                    : module.id === 'lms'
                                      ? 'lms'
                                      : undefined;

                        return (
                            <div
                                key={module.id}
                                id={sectionId}
                                className="scroll-mt-28 grid items-center gap-10 lg:grid-cols-2 lg:gap-16"
                            >
                                <Reveal
                                    className={index % 2 === 1 ? 'lg:order-2' : ''}
                                    delay={0.05}
                                >
                                    <span className="rounded-full bg-navy-900/5 px-3 py-1 text-xs font-bold uppercase tracking-wider text-navy-900 dark:bg-white/10 dark:text-brand-yellow">
                                        {module.badge}
                                    </span>
                                    <h3 className="mt-4 font-display text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">
                                        {module.title}
                                    </h3>
                                    <p className="mt-4 text-slate-600 dark:text-slate-400">
                                        {module.description}
                                    </p>
                                    <ul className="mt-6 space-y-3">
                                        {module.highlights.map((h) => (
                                            <li
                                                key={h}
                                                className="flex items-start gap-3 text-sm text-slate-700 dark:text-slate-300"
                                            >
                                                <Check className="mt-0.5 h-5 w-5 shrink-0 text-emerald-500" />
                                                {h}
                                            </li>
                                        ))}
                                    </ul>
                                </Reveal>
                                <Reveal
                                    className={index % 2 === 1 ? 'lg:order-1' : ''}
                                    delay={0.12}
                                >
                                    <FeatureVisual module={module} />
                                </Reveal>
                            </div>
                        );
                    })}
                </div>
            </div>
        </section>
    );
}
