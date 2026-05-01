import { Link } from '@inertiajs/react';

type AboutSectionProps = {
    /** Si true, muestra versión corta para la home */
    condensed?: boolean;
};

export function AboutSection({ condensed = false }: AboutSectionProps) {
    return (
        <section className="bg-white py-16 sm:py-24" id="nosotros">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="grid gap-12 lg:grid-cols-2 lg:items-center">
                    <div>
                        <p className="text-xs font-bold uppercase tracking-widest text-brand-red">
                            Nosotros
                        </p>
                        <h2 className="mt-2 text-3xl font-bold tracking-tight text-navy-900 sm:text-4xl">
                            Una comunidad que aprende y crece junta
                        </h2>
                        <p className="mt-4 text-plomo leading-relaxed">
                            {condensed
                                ? 'Más de una década formando líderes con pensamiento crítico, empatía y responsabilidad social. Nuestro equipo docente se actualiza de forma continua.'
                                : 'En Horizonte creemos que cada estudiante tiene talentos únicos. Acompañamos su desarrollo académico, emocional y social con proyectos interdisciplinarios, deportes, arte y valores cristianos que guían la convivencia.'}
                        </p>
                        {!condensed && (
                            <>
                                <p className="mt-4 text-plomo leading-relaxed">
                                    Nuestra propuesta integra tecnología educativa,
                                    evaluación formativa y comunicación fluida con
                                    las familias para construir un entorno de
                                    confianza.
                                </p>
                                <ul className="mt-6 grid gap-3 text-sm font-medium text-navy-900 sm:grid-cols-2">
                                    {[
                                        'Misión y visión claras',
                                        'Directorio y pastoral',
                                        'Ambientes seguros y vigilados',
                                        'Consejo educativo activo',
                                    ].map((item) => (
                                        <li
                                            key={item}
                                            className="flex items-center gap-2 rounded-lg bg-navy-50 px-3 py-2"
                                        >
                                            <span className="h-1.5 w-1.5 rounded-full bg-brand-yellow" />
                                            {item}
                                        </li>
                                    ))}
                                </ul>
                            </>
                        )}
                        {condensed && (
                            <Link
                                href={route('public.nosotros')}
                                className="mt-6 inline-block text-sm font-semibold text-brand-red underline-offset-4 hover:underline"
                            >
                                Ver más sobre nosotros
                            </Link>
                        )}
                    </div>
                    <div className="relative">
                        <div className="aspect-[4/3] overflow-hidden rounded-2xl bg-gradient-to-br from-navy-100 to-navy-50 shadow-xl ring-1 ring-navy-900/10">
                            <div className="flex h-full flex-col justify-end p-8">
                                <p className="text-sm font-medium text-navy-900/80">
                                    Campus y aulas preparadas para el aprendizaje
                                    activo (imagen institucional — demo).
                                </p>
                            </div>
                        </div>
                        <div className="absolute -bottom-4 -right-4 hidden h-24 w-24 rounded-xl bg-brand-yellow/90 shadow-lg sm:block" />
                    </div>
                </div>
            </div>
        </section>
    );
}
