import { Award } from 'lucide-react';
import { Reveal } from '@/Components/Public/Premium/Reveal';
import { achievements } from '@/Components/Public/institutionalSiteData';

export function AchievementsSection() {
    return (
        <section className="bg-gradient-to-b from-navy-900 to-navy-950 py-16 text-white sm:py-24">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <Reveal className="mx-auto max-w-2xl text-center">
                    <p className="text-xs font-bold uppercase tracking-widest text-brand-yellow">
                        Logros y reconocimientos
                    </p>
                    <h2 className="mt-2 font-display text-3xl font-bold sm:text-4xl">
                        Excelencia académica con resultados
                    </h2>
                    <p className="mt-4 text-white/75">
                        Destacamos el esfuerzo de estudiantes y docentes en competencias,
                        certificaciones y proyección universitaria.
                    </p>
                </Reveal>
                <ul className="mt-12 grid gap-4 sm:grid-cols-2">
                    {achievements.map((item, i) => (
                        <Reveal key={item} delay={i * 0.06}>
                            <li className="flex gap-4 rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                                <Award className="h-6 w-6 shrink-0 text-brand-yellow" />
                                <span className="text-sm font-medium leading-relaxed text-white/90">
                                    {item}
                                </span>
                            </li>
                        </Reveal>
                    ))}
                </ul>
            </div>
        </section>
    );
}
