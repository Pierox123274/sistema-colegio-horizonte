import { Link } from '@inertiajs/react';
import { BookOpen, GraduationCap, Sparkles } from 'lucide-react';

const levels = [
    {
        key: 'inicial',
        title: 'Inicial',
        icon: Sparkles,
        grades: '3, 4 y 5 años',
        description:
            'Estimulación temprana, juego simbólico y rutinas de autonomía. Bases sólidas en comunicación y convivencia.',
        accent: 'border-brand-yellow/60 bg-brand-yellow/5',
    },
    {
        key: 'primaria',
        title: 'Primaria',
        icon: BookOpen,
        grades: '1.º al 6.º grado',
        description:
            'Lectoescritura, matemática con sentido, ciencias y ciudadanía. Proyectos STEAM y hábitos de estudio.',
        accent: 'border-brand-red/40 bg-brand-red/5',
    },
    {
        key: 'secundaria',
        title: 'Secundaria',
        icon: GraduationCap,
        grades: '1.º al 5.º grado',
        description:
            'Pensamiento crítico, orientación vocacional y preparación para la vida universitaria y ciudadana.',
        accent: 'border-navy-900/30 bg-navy-50',
    },
];

type EducationalLevelsSectionProps = {
    showLinkToFull?: boolean;
};

export function EducationalLevelsSection({
    showLinkToFull = true,
}: EducationalLevelsSectionProps) {
    return (
        <section
            className="section-institutional-alt section-separator scroll-mt-24 py-16 sm:py-24"
            id="niveles"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="mx-auto max-w-2xl text-center">
                    <p className="text-xs font-bold uppercase tracking-widest text-brand-red">
                        Niveles educativos
                    </p>
                    <h2 className="mt-2 text-3xl font-bold tracking-tight text-navy-900 dark:text-white sm:text-4xl">
                        Tres etapas, una misma excelencia
                    </h2>
                    <p className="mt-4 text-plomo">
                        Progresión curricular alineada al marco nacional, con
                        enfoque personalizado y seguimiento por tutorías.
                    </p>
                </div>
                <div className="mt-14 grid gap-6 md:grid-cols-3">
                    {levels.map((level) => (
                        <article
                            key={level.key}
                            id={`nivel-${level.key}`}
                            className={`scroll-mt-24 rounded-2xl border-2 bg-white p-6 shadow-institutional transition duration-300 hover:-translate-y-1 hover:shadow-institutional-lg dark:bg-slate-900 ${level.accent}`}
                        >
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-navy-900 text-white">
                                <level.icon className="h-6 w-6" strokeWidth={1.75} />
                            </div>
                            <h3 className="mt-5 text-xl font-bold text-navy-900">
                                {level.title}
                            </h3>
                            <p className="mt-1 text-sm font-semibold text-brand-red">
                                {level.grades}
                            </p>
                            <p className="mt-3 text-sm leading-relaxed text-plomo">
                                {level.description}
                            </p>
                        </article>
                    ))}
                </div>
                {showLinkToFull && (
                    <p className="mt-10 text-center">
                        <Link
                            href={route('public.niveles')}
                            className="text-sm font-semibold text-navy-900 underline-offset-4 hover:underline"
                        >
                            Ver detalle de niveles y propuesta por etapa
                        </Link>
                    </p>
                )}
            </div>
        </section>
    );
}
