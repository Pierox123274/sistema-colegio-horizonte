import { Reveal } from '@/Components/Public/Premium/Reveal';
import { AnimatedCounter } from '@/Components/Public/Premium/AnimatedCounter';

const defaultStats = [
    { value: 15, suffix: '+', label: 'Años de experiencia' },
    { value: 3, suffix: '', label: 'Niveles educativos' },
    { value: 850, suffix: '+', label: 'Familias en comunidad' },
    { value: 45, suffix: '+', label: 'Docentes especializados' },
];

type StatItem = { value: number; suffix?: string; label: string };

export function PublicStatsRow({
    variant = 'hero',
    items,
}: {
    variant?: 'hero' | 'section';
    items?: StatItem[];
}) {
    const stats = items && items.length > 0 ? items : defaultStats;
    const isHero = variant === 'hero';
    return (
        <div
            className={
                isHero
                    ? 'border-y border-white/10 bg-[#071526]/40 backdrop-blur-md'
                    : 'section-institutional-alt border-y border-slate-200/60 dark:border-white/10'
            }
        >
            <div className="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 py-8 sm:px-6 lg:grid-cols-4 lg:px-8">
                {stats.map((s, i) => (
                    <Reveal key={s.label} delay={i * 0.06} className="text-center">
                        <p
                            className={`text-2xl font-extrabold sm:text-3xl ${
                                isHero ? 'text-amber-400' : 'text-slate-900 dark:text-amber-400'
                            }`}
                        >
                            <AnimatedCounter value={s.value} suffix={s.suffix} />
                        </p>
                        <p
                            className={`mt-1 text-xs font-medium sm:text-sm ${
                                isHero ? 'text-white/75' : 'text-slate-600 dark:text-slate-400'
                            }`}
                        >
                            {s.label}
                        </p>
                    </Reveal>
                ))}
            </div>
        </div>
    );
}
