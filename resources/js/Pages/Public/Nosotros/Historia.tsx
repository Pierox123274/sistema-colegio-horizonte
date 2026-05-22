import { Link } from '@inertiajs/react';
import { PageHero } from '@/Components/Public/ui/PageHero';
import { SectionHeader } from '@/Components/Public/ui/SectionHeader';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

export default function Historia() {
    return (
        <PublicLayout title="Historia — I.E.P. Horizonte" description="Legado y evolución del colegio.">
            <PageHero
                title="Nuestra historia"
                subtitle="Más de quince años construyendo una comunidad educativa de confianza."
                breadcrumbs={[
                    { label: 'Nosotros', href: route('public.nosotros') },
                    { label: 'Historia' },
                ]}
            />
            <section className="section-institutional py-16 sm:py-20">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <SectionHeader
                        title="Tradición que evoluciona"
                        description="Horizonte nació con la convicción de ofrecer educación privada de calidad con valores sólidos. Desde nuestros primeros años hemos ampliado infraestructura, fortalecido el cuerpo docente y consolidado resultados académicos que nos posicionan en la región (contenido demo)."
                    />
                    <div className="mt-8 space-y-4 text-plomo leading-relaxed dark:text-slate-300">
                        <p>
                            Cada promoción lleva consigo el sello de excelencia académica y servicio a la
                            comunidad que caracteriza a nuestros egresados.
                        </p>
                        <p>
                            Hoy seguimos invirtiendo en formación docente, innovación pedagógica y espacios
                            que inspiran el aprendizaje.
                        </p>
                    </div>
                    <Link
                        href={route('public.nosotros.mision-vision')}
                        className="mt-8 inline-flex text-sm font-semibold text-institutional-blue-900 hover:underline dark:text-institutional-gold"
                    >
                        Conocer misión y visión →
                    </Link>
                </div>
            </section>
            <InstitutionalCTA primaryLabel="Conocer admisión" />
        </PublicLayout>
    );
}
