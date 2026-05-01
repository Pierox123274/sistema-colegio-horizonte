import { Link } from '@inertiajs/react';
import { CheckCircle2 } from 'lucide-react';

const steps = [
    'Entrevista familiar y visita guiada al campus (demo).',
    'Evaluación diagnóstica acorde al grado de ingreso.',
    'Entrega de documentación y constancia de vacante.',
    'Matrícula y bienvenida al aula asignada.',
];

type AdmissionSectionProps = {
    condensed?: boolean;
};

export function AdmissionSection({ condensed = false }: AdmissionSectionProps) {
    return (
        <section className="bg-gradient-to-b from-white to-navy-50 py-16 sm:py-24" id="admision">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="grid gap-12 lg:grid-cols-2 lg:items-center">
                    <div>
                        <p className="text-xs font-bold uppercase tracking-widest text-brand-red">
                            Admisión {new Date().getFullYear()}
                        </p>
                        <h2 className="mt-2 text-3xl font-bold tracking-tight text-navy-900 sm:text-4xl">
                            Un proceso claro para tu familia
                        </h2>
                        <p className="mt-4 text-plomo leading-relaxed">
                            {condensed
                                ? 'Cupos limitados por grado. Te acompañamos en cada paso con orientación personalizada.'
                                : 'Abrimos vacantes según disponibilidad por nivel. Priorizamos la adecuación del estudiante al proyecto educativo y el diálogo transparente con apoderados.'}
                        </p>
                        <ul className="mt-8 space-y-3">
                            {steps.slice(0, condensed ? 2 : steps.length).map((s) => (
                                <li
                                    key={s}
                                    className="flex gap-3 text-sm font-medium text-navy-900"
                                >
                                    <CheckCircle2 className="h-5 w-5 shrink-0 text-emerald-600" />
                                    {s}
                                </li>
                            ))}
                        </ul>
                        <Link
                            href={route('public.admision')}
                            className="mt-8 inline-flex rounded-xl bg-navy-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-navy-950"
                        >
                            Ver proceso completo
                        </Link>
                    </div>
                    <div className="rounded-2xl border border-plomo/10 bg-navy-900 p-8 text-white shadow-xl">
                        <p className="text-sm font-semibold uppercase tracking-wider text-brand-yellow">
                            Próximas fechas (demo)
                        </p>
                        <ul className="mt-6 space-y-4 text-sm text-white/85">
                            <li className="flex justify-between border-b border-white/10 pb-3">
                                <span>Día de puertas abiertas</span>
                                <span className="font-semibold text-brand-yellow">
                                    12 Abr
                                </span>
                            </li>
                            <li className="flex justify-between border-b border-white/10 pb-3">
                                <span>Inicio de matrícula regular</span>
                                <span className="font-semibold text-brand-yellow">
                                    5 May
                                </span>
                            </li>
                            <li className="flex justify-between">
                                <span>Cierre de vacantes estimado</span>
                                <span className="font-semibold text-brand-yellow">
                                    30 Jun
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    );
}
