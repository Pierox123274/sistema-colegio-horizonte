import { CheckCircle2, FileText } from 'lucide-react';
import { PageHero } from '@/Components/Public/ui/PageHero';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

const requirements = [
    'Partida de nacimiento y DNI del postulante.',
    'DNI de apoderados y ficha de datos familiares.',
    'Certificado de estudios o constancia del colegio de procedencia.',
    'Fotografías tamaño carnet y carta de motivación (secundaria).',
    'Evaluación diagnóstica y entrevista familiar programadas.',
];

export default function AdmisionRequisitos() {
    return (
        <PublicLayout title="Requisitos de admisión — I.E.P. Horizonte" description="Documentación.">
            <PageHero
                title="Requisitos"
                subtitle="Documentación y condiciones para postular por nivel."
                breadcrumbs={[
                    { label: 'Admisión', href: route('public.admision') },
                    { label: 'Requisitos' },
                ]}
            />
            <section className="section-institutional py-16 sm:py-20">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center gap-3">
                        <FileText className="h-8 w-8 text-institutional-gold" />
                        <h2 className="font-display text-2xl font-bold text-institutional-blue-900 dark:text-white">
                            Checklist de postulación
                        </h2>
                    </div>
                    <ul className="mt-8 space-y-4">
                        {requirements.map((r) => (
                            <li key={r} className="flex gap-3 text-plomo dark:text-slate-300">
                                <CheckCircle2 className="h-5 w-5 shrink-0 text-emerald-600" />
                                {r}
                            </li>
                        ))}
                    </ul>
                </div>
            </section>
            <InstitutionalCTA primaryHref={route('public.admision.matricula')} primaryLabel="Matrícula 2026" />
        </PublicLayout>
    );
}
