import { Link } from '@inertiajs/react';
import { CheckCircle2, MessageCircle, FileText, Calendar, ClipboardList } from 'lucide-react';
import { Reveal } from '@/Components/Public/Premium/Reveal';
import { admissionTimeline } from '@/Components/Public/nav/publicNavConfig';
import { PublicSectionHeader } from './PublicSectionHeader';
import { publicImage } from '@/Components/Public/data/publicImages';

const requirements = [
    { icon: FileText, title: 'Documentación', text: 'Partida, DNI, certificados y ficha familiar.' },
    { icon: ClipboardList, title: 'Evaluación', text: 'Diagnóstico acorde al grado de ingreso.' },
    { icon: Calendar, title: 'Entrevista', text: 'Visita guiada y diálogo con apoderados.' },
    { icon: CheckCircle2, title: 'Matrícula', text: 'Confirmación de vacante y bienvenida.' },
];

export function AdmissionPremiumSection() {
    return (
        <>
            <section className="relative overflow-hidden py-20 sm:py-28">
                <img
                    src={publicImage('heroAdmision')}
                    alt=""
                    className="absolute inset-0 h-full w-full object-cover opacity-20 dark:opacity-30"
                />
                <div className="absolute inset-0 bg-institutional-surface/95 dark:bg-slate-950/92" />
                <div className="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <PublicSectionHeader
                        eyebrow="Admisión 2026"
                        title="Tu familia, bienvenida al proceso"
                        description="Transparencia, acompañamiento y fechas claras en cada etapa."
                        align="center"
                        className="mb-14"
                    />

                    <div className="grid gap-10 lg:grid-cols-2">
                        <Reveal>
                            <h3 className="font-display text-xl font-bold text-slate-900 dark:text-white">
                                Cronograma
                            </h3>
                            <ul className="relative mt-8">
                                {admissionTimeline.map((step, i) => (
                                    <li key={step.title} className="relative flex gap-4 pb-8 last:pb-0">
                                        {i < admissionTimeline.length - 1 ? (
                                            <span
                                                className="absolute left-[1.1rem] top-10 h-[calc(100%-1rem)] w-px bg-amber-400/50"
                                                aria-hidden
                                            />
                                        ) : null}
                                        <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#0f2847] text-sm font-bold text-amber-400 ring-2 ring-amber-400/30">
                                            {step.date}
                                        </span>
                                        <div>
                                            <p className="font-semibold text-slate-900 dark:text-white">
                                                {step.title}
                                            </p>
                                            <p className="text-sm text-slate-600 dark:text-slate-300">
                                                {step.desc}
                                            </p>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        </Reveal>

                        <Reveal delay={0.1}>
                            <h3 className="font-display text-xl font-bold text-slate-900 dark:text-white">
                                Requisitos
                            </h3>
                            <div className="mt-6 grid gap-3 sm:grid-cols-2">
                                {requirements.map((r) => (
                                    <div
                                        key={r.title}
                                        className="rounded-2xl border border-slate-200/70 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-slate-900/80"
                                    >
                                        <r.icon className="h-6 w-6 text-amber-500 dark:text-amber-400" />
                                        <p className="mt-3 font-semibold text-slate-900 dark:text-white">
                                            {r.title}
                                        </p>
                                        <p className="mt-1 text-sm text-slate-600 dark:text-slate-300">
                                            {r.text}
                                        </p>
                                    </div>
                                ))}
                            </div>
                            <Link
                                href={route('public.admision.requisitos')}
                                className="mt-4 inline-block text-sm font-semibold text-amber-600 hover:underline dark:text-amber-400"
                            >
                                Ver requisitos completos →
                            </Link>
                        </Reveal>
                    </div>
                </div>
            </section>

            <section className="border-y border-slate-200/60 bg-[#0f2847] py-14 dark:border-white/10">
                <div className="mx-auto flex max-w-7xl flex-col items-center justify-between gap-6 px-4 text-center sm:flex-row sm:text-left sm:px-6 lg:px-8">
                    <div>
                        <p className="font-display text-2xl font-bold text-white sm:text-3xl">
                            Postula a la admisión 2026
                        </p>
                        <p className="mt-2 text-slate-300">Cupos limitados · Agenda tu visita guiada</p>
                    </div>
                    <div className="flex flex-wrap justify-center gap-3">
                        <Link
                            href={route('public.contacto')}
                            className="inline-flex items-center gap-2 rounded-2xl bg-amber-400 px-8 py-4 text-sm font-bold text-[#071526] shadow-lg transition hover:bg-amber-300"
                        >
                            Solicitar información
                        </Link>
                        <a
                            href="https://wa.me/51999999999"
                            target="_blank"
                            rel="noreferrer"
                            className="inline-flex items-center gap-2 rounded-2xl border border-white/25 px-8 py-4 text-sm font-semibold text-white transition hover:bg-white/10"
                        >
                            <MessageCircle className="h-5 w-5 text-emerald-400" />
                            WhatsApp (demo)
                        </a>
                    </div>
                </div>
            </section>
        </>
    );
}
