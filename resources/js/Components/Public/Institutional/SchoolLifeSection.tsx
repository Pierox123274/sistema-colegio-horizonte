import {
    Award,
    Calendar,
    Heart,
    Palette,
    Trophy,
} from 'lucide-react';
import type { LucideIcon } from 'lucide-react';
import { Reveal } from '@/Components/Public/Premium/Reveal';

const vidaItems: {
    id: string;
    title: string;
    description: string;
    icon: LucideIcon;
    span?: string;
}[] = [
    {
        id: 'vida-deportes',
        title: 'Deportes',
        description: 'Equipos intercolegiales, educación física y hábitos saludables.',
        icon: Trophy,
        span: 'sm:col-span-2 sm:row-span-2',
    },
    {
        id: 'vida-arte',
        title: 'Actividades artísticas',
        description: 'Teatro, música, danza y muestras por nivel.',
        icon: Palette,
    },
    {
        id: 'vida-eventos',
        title: 'Eventos institucionales',
        description: 'Ceremonias, aniversarios y celebraciones con sentido.',
        icon: Calendar,
    },
    {
        id: 'vida-concursos',
        title: 'Concursos y ferias',
        description: 'Ciencia, emprendimiento y olimpiadas académicas.',
        icon: Award,
    },
    {
        id: 'vida-pastoral',
        title: 'Pastoral',
        description: 'Tutorías, mediación y acompañamiento espiritual.',
        icon: Heart,
    },
    {
        id: 'vida-talleres',
        title: 'Talleres formativos',
        description: 'Clubes de lectura, robótica y proyectos especiales.',
        icon: Palette,
    },
];

const masonryTiles = [
    'col-span-2 row-span-2 from-institutional-blue-900/85 to-institutional-blue-800/70',
    'from-institutional-gold/70 to-amber-500/50',
    'from-sky-500/50 to-institutional-accent/40',
    'from-institutional-blue-800/60 to-slate-700/50',
];

export function SchoolLifeSection() {
    return (
        <section
            id="vida-escolar"
            className="section-institutional-alt section-separator scroll-mt-24 py-16 sm:py-24"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <Reveal className="mx-auto max-w-3xl text-center">
                    <p className="text-xs font-bold uppercase tracking-widest text-institutional-gold">
                        Vida escolar
                    </p>
                    <h2 className="mt-2 font-display text-3xl font-bold text-institutional-blue-900 dark:text-white sm:text-4xl">
                        Aprender, convivir y celebrar en comunidad
                    </h2>
                    <p className="mt-4 text-plomo dark:text-slate-400">
                        Experiencias que fortalecen talentos, amistades y liderazgo con sentido
                        humano.
                    </p>
                </Reveal>

                <div className="mt-14 grid gap-8 lg:grid-cols-5">
                    <div className="grid grid-cols-2 grid-rows-3 gap-2 lg:col-span-2">
                        {masonryTiles.map((tone, i) => (
                            <div
                                key={i}
                                className={`rounded-2xl bg-gradient-to-br ${tone} min-h-[5rem] transition duration-500 hover:scale-[1.02] hover:opacity-100 opacity-90 ${
                                    i === 0 ? 'col-span-2 row-span-2 min-h-[12rem]' : ''
                                }`}
                                aria-hidden
                            />
                        ))}
                    </div>
                    <div className="grid gap-4 sm:grid-cols-2 lg:col-span-3">
                        {vidaItems.map((item, i) => {
                            const Icon = item.icon;
                            return (
                                <Reveal key={item.id} delay={i * 0.05}>
                                    <article
                                        id={item.id}
                                        className={`group scroll-mt-24 rounded-2xl border border-slate-200/70 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:border-institutional-gold/40 hover:shadow-institutional dark:border-white/10 dark:bg-slate-900/60 ${item.span ?? ''}`}
                                    >
                                        <span className="flex h-10 w-10 items-center justify-center rounded-xl bg-institutional-blue-900/5 text-institutional-blue-900 transition group-hover:bg-institutional-gold/15 group-hover:text-institutional-gold dark:bg-white/5 dark:text-institutional-gold">
                                            <Icon className="h-5 w-5" />
                                        </span>
                                        <h3 className="mt-3 font-bold text-institutional-blue-900 dark:text-white">
                                            {item.title}
                                        </h3>
                                        <p className="mt-2 text-sm text-plomo dark:text-slate-400">
                                            {item.description}
                                        </p>
                                    </article>
                                </Reveal>
                            );
                        })}
                    </div>
                </div>
            </div>
        </section>
    );
}
