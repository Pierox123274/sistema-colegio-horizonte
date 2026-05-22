import { Link } from '@inertiajs/react';
import { CheckCircle2, FileText, Mail } from 'lucide-react';
import { InstitutionalButtonLink } from '@/Components/Public/ui/InstitutionalButton';
import { admissionTimeline } from '@/Components/Public/nav/publicNavConfig';

const steps = [
    'Entrevista familiar y visita guiada al campus.',
    'Evaluación diagnóstica acorde al grado de ingreso.',
    'Entrega de documentación y constancia de vacante.',
    'Matrícula y bienvenida al aula asignada.',
];

const requirements = [
    'Partida de nacimiento y DNI del postulante.',
    'DNI de apoderados y ficha de datos familiares.',
    'Certificado de estudios o constancia del colegio de procedencia.',
    'Fotografías tamaño carnet y carta de motivación (secundaria).',
];

type AdmissionSectionProps = {
    condensed?: boolean;
};

export function AdmissionSection({ condensed = false }: AdmissionSectionProps) {
    return (
        <section
            className="section-institutional section-separator scroll-mt-24 py-16 sm:py-24"
            id="admision"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="mx-auto max-w-2xl text-center">
                    <p className="text-xs font-bold uppercase tracking-widest text-institutional-gold">
                        Admisión 2026
                    </p>
                    <h2 className="mt-2 font-display text-3xl font-bold tracking-tight text-institutional-blue-900 dark:text-white sm:text-4xl">
                        Un proceso claro para tu familia
                    </h2>
                    <p className="mt-4 text-plomo">
                        Cupos limitados por grado. Te acompañamos con orientación personalizada en
                        cada etapa.
                    </p>
                </div>

                <div
                    id="admision-proceso"
                    className="mt-14 scroll-mt-24 grid gap-12 lg:grid-cols-2 lg:items-start"
                >
                    <div>
                        <h3 className="font-display text-xl font-bold text-institutional-blue-900 dark:text-white">
                            Proceso de admisión
                        </h3>
                        <ul className="mt-6 space-y-3">
                            {steps.slice(0, condensed ? 3 : steps.length).map((s, i) => (
                                <li
                                    key={s}
                                    className="flex gap-3 text-sm font-medium text-institutional-blue-900 dark:text-slate-200"
                                >
                                    <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-institutional-gold/20 text-xs font-bold text-institutional-blue-900">
                                        {i + 1}
                                    </span>
                                    {s}
                                </li>
                            ))}
                        </ul>
                        <InstitutionalButtonLink
                            href={route('public.admision')}
                            variant="ghost"
                            className="mt-8"
                        >
                            Ver página completa
                        </InstitutionalButtonLink>
                    </div>

                    <div
                        id="admision-cronograma"
                        className="scroll-mt-24 rounded-2xl border border-slate-200/80 bg-gradient-to-br from-institutional-blue-900 to-institutional-blue-800 p-8 text-white shadow-institutional-lg dark:border-white/10"
                    >
                        <p className="text-sm font-semibold uppercase tracking-wider text-institutional-gold-light">
                            Cronograma 2026
                        </p>
                        <ul className="relative mt-8 space-y-0">
                            {admissionTimeline.map((step, i) => (
                                <li key={step.title} className="relative flex gap-4 pb-6 last:pb-0">
                                    {i < admissionTimeline.length - 1 ? (
                                        <span
                                            className="absolute left-[1.1rem] top-9 h-[calc(100%-1.25rem)] w-px bg-gradient-to-b from-institutional-gold/70 to-transparent"
                                            aria-hidden
                                        />
                                    ) : null}
                                    <span className="relative z-10 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-institutional-gold text-xs font-bold text-institutional-blue-950">
                                        {step.date}
                                    </span>
                                    <div>
                                        <p className="font-semibold">{step.title}</p>
                                        <p className="text-sm text-white/75">{step.desc}</p>
                                    </div>
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>

                <div
                    id="admision-requisitos"
                    className="mt-12 scroll-mt-24 rounded-2xl border border-slate-200/60 bg-institutional-surface-alt p-8 dark:border-white/10 dark:bg-slate-900/50"
                >
                    <div className="flex items-center gap-3">
                        <FileText className="h-6 w-6 text-institutional-gold" />
                        <h3 className="font-display text-xl font-bold text-institutional-blue-900 dark:text-white">
                            Requisitos de postulación
                        </h3>
                    </div>
                    <ul className="mt-6 grid gap-3 sm:grid-cols-2">
                        {requirements.map((r) => (
                            <li key={r} className="flex gap-2 text-sm text-plomo dark:text-slate-300">
                                <CheckCircle2 className="h-5 w-5 shrink-0 text-emerald-600/90" />
                                {r}
                            </li>
                        ))}
                    </ul>
                </div>

                <div
                    id="contacto-admision"
                    className="mt-10 flex scroll-mt-24 flex-col items-center justify-between gap-4 rounded-2xl border border-institutional-gold/30 bg-institutional-gold-soft/40 p-6 sm:flex-row dark:border-institutional-gold/20 dark:bg-institutional-gold/10"
                >
                    <div>
                        <p className="font-semibold text-institutional-blue-900 dark:text-white">
                            ¿Agendamos tu visita?
                        </p>
                        <p className="text-sm text-plomo">admision@horizonte.edu.pe · +51 1 234 5678</p>
                    </div>
                    <Link
                        href={route('public.contacto')}
                        className="inline-flex items-center gap-2 rounded-2xl bg-institutional-blue-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-institutional-blue-800"
                    >
                        <Mail className="h-4 w-4 text-institutional-gold" />
                        Contacto admisiones
                    </Link>
                </div>
            </div>
        </section>
    );
}
