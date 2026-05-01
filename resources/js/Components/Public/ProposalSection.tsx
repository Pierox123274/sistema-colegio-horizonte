import { Target, Users2, Lightbulb } from 'lucide-react';

const pillars = [
    {
        title: 'Aprendizaje significativo',
        text: 'Secuencias didácticas centradas en competencias y evidencias de aprendizaje.',
        icon: Lightbulb,
    },
    {
        title: 'Formación en valores',
        text: 'Convivencia armónica, liderazgo servicial y responsabilidad con la comunidad.',
        icon: Users2,
    },
    {
        title: 'Excelencia medible',
        text: 'Seguimiento académico, retroalimentación oportuna y metas claras por ciclo.',
        icon: Target,
    },
];

export function ProposalSection() {
    return (
        <section className="bg-white py-16 sm:py-24">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="mx-auto max-w-2xl text-center">
                    <p className="text-xs font-bold uppercase tracking-widest text-brand-red">
                        Propuesta educativa
                    </p>
                    <h2 className="mt-2 text-3xl font-bold tracking-tight text-navy-900 sm:text-4xl">
                        Aprender haciendo, crecer sirviendo
                    </h2>
                    <p className="mt-4 text-plomo">
                        Integramos currículo nacional, inglés progresivo,
                        tecnología aplicada y espacios de arte y deporte para un
                        desarrollo equilibrado (contenido demo).
                    </p>
                </div>
                <div className="mt-14 grid gap-8 md:grid-cols-3">
                    {pillars.map((p) => (
                        <div
                            key={p.title}
                            className="rounded-2xl border border-plomo/10 bg-navy-50/50 p-6 text-center transition duration-300 hover:border-brand-yellow/40"
                        >
                            <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-white text-navy-900 shadow-md">
                                <p.icon className="h-7 w-7" strokeWidth={1.5} />
                            </div>
                            <h3 className="mt-5 text-lg font-bold text-navy-900">
                                {p.title}
                            </h3>
                            <p className="mt-2 text-sm leading-relaxed text-plomo">
                                {p.text}
                            </p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
