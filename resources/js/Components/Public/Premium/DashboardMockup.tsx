import { lazy, Suspense } from 'react';
import { motion } from 'framer-motion';
import {
    BookOpen,
    Brain,
    CalendarCheck,
    ClipboardCheck,
    Sparkles,
    TrendingUp,
} from 'lucide-react';
import { usePublicTheme } from './PublicThemeProvider';
import { AnimatedCounter } from './AnimatedCounter';

const MiniAnalyticsChart = lazy(() =>
    import('./MiniAnalyticsChart').then((m) => ({ default: m.MiniAnalyticsChart })),
);

export function DashboardMockup() {
    const { isDark } = usePublicTheme();

    return (
        <motion.div
            className="relative mx-auto w-full max-w-xl lg:max-w-none"
            initial={{ opacity: 0, y: 32, rotateX: 8 }}
            animate={{ opacity: 1, y: 0, rotateX: 0 }}
            transition={{ duration: 0.8, delay: 0.2, ease: [0.22, 1, 0.36, 1] }}
        >
            <div className="absolute -inset-4 rounded-3xl bg-gradient-to-br from-brand-yellow/30 via-transparent to-brand-red/20 blur-2xl" />
            <div className="glass-panel relative overflow-hidden rounded-2xl border-2 border-white/30 p-4 shadow-premium dark:border-white/10 sm:p-5">
                <div className="mb-4 flex items-center justify-between gap-2">
                    <div className="flex gap-1.5">
                        <span className="h-3 w-3 rounded-full bg-rose-400" />
                        <span className="h-3 w-3 rounded-full bg-amber-400" />
                        <span className="h-3 w-3 rounded-full bg-emerald-400" />
                    </div>
                    <span className="rounded-full bg-navy-900/5 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-navy-900 dark:bg-white/10 dark:text-white/80">
                        Panel institucional
                    </span>
                </div>

                <div className="grid gap-3 sm:grid-cols-2">
                    <div className="rounded-xl bg-gradient-to-br from-navy-900 to-navy-950 p-4 text-white">
                        <div className="flex items-center gap-2 text-brand-yellow">
                            <TrendingUp className="h-4 w-4" />
                            <span className="text-xs font-medium">Rendimiento</span>
                        </div>
                        <p className="mt-2 text-2xl font-bold">
                            <AnimatedCounter value={92} suffix="%" />
                        </p>
                        <p className="text-xs text-white/60">Promedio sección 4°A</p>
                    </div>
                    <div className="rounded-xl border border-slate-200/80 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-900/50">
                        <div className="flex items-center gap-2 text-violet-600 dark:text-violet-300">
                            <Brain className="h-4 w-4" />
                            <span className="text-xs font-medium">Adaptativo</span>
                        </div>
                        <p className="mt-2 text-sm font-semibold text-slate-800 dark:text-white">
                            Ruta personalizada
                        </p>
                        <div className="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                            <div className="h-full w-3/4 rounded-full bg-gradient-to-r from-violet-500 to-brand-yellow" />
                        </div>
                    </div>
                </div>

                <div className="mt-3 grid grid-cols-4 gap-2">
                    {[
                        { icon: ClipboardCheck, label: 'Notas' },
                        { icon: BookOpen, label: 'LMS' },
                        { icon: Sparkles, label: 'IA' },
                        { icon: CalendarCheck, label: 'Agenda' },
                    ].map(({ icon: Icon, label }) => (
                        <div
                            key={label}
                            className="flex flex-col items-center gap-1 rounded-lg bg-slate-50 py-2 dark:bg-white/5"
                        >
                            <Icon className="h-4 w-4 text-navy-900 dark:text-brand-yellow" />
                            <span className="text-[10px] font-medium text-slate-600 dark:text-slate-300">
                                {label}
                            </span>
                        </div>
                    ))}
                </div>

                <div className="mt-3 rounded-xl border border-slate-200/80 bg-white/90 p-3 dark:border-white/10 dark:bg-slate-900/40">
                    <p className="mb-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                        Analítica — últimos meses
                    </p>
                    <Suspense fallback={<div className="h-32 animate-pulse rounded-lg bg-slate-100 dark:bg-slate-800" />}>
                        <MiniAnalyticsChart variant="area" dark={isDark} />
                    </Suspense>
                </div>
            </div>
        </motion.div>
    );
}
