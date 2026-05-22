import { PageHero } from '@/Components/Public/ui/PageHero';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

const eventos = [
    { title: 'Ceremonia de promoción', date: 'Diciembre' },
    { title: 'Aniversario institucional', date: 'Mayo' },
    { title: 'Día de la familia Horizonte', date: 'Agosto' },
    { title: 'Misa de acción de gracias', date: 'Noviembre' },
];

export default function Eventos() {
    return (
        <PublicLayout title="Eventos — Vida escolar" description="Celebraciones institucionales.">
            <PageHero
                title="Eventos institucionales"
                subtitle="Momentos que fortalecen identidad y sentido de pertenencia."
                breadcrumbs={[
                    { label: 'Vida escolar', href: route('public.vida-escolar') },
                    { label: 'Eventos' },
                ]}
            />
            <section className="section-institutional py-16 sm:py-20">
                <div className="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                    <ul className="space-y-4">
                        {eventos.map((e) => (
                            <li
                                key={e.title}
                                className="flex items-center justify-between rounded-2xl border border-slate-200/70 bg-white px-6 py-4 dark:border-white/10 dark:bg-slate-900/60"
                            >
                                <span className="font-semibold text-institutional-blue-900 dark:text-white">
                                    {e.title}
                                </span>
                                <span className="text-sm font-medium text-institutional-gold">{e.date}</span>
                            </li>
                        ))}
                    </ul>
                </div>
            </section>
            <InstitutionalCTA secondaryLabel="Galería" secondaryHref={route('public.galeria')} />
        </PublicLayout>
    );
}
