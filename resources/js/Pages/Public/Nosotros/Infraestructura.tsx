import { InfrastructureSection } from '@/Components/Public/Institutional/InfrastructureSection';
import { PageHero } from '@/Components/Public/ui/PageHero';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

export default function Infraestructura() {
    return (
        <PublicLayout title="Infraestructura — I.E.P. Horizonte" description="Campus y espacios.">
            <PageHero
                title="Infraestructura"
                subtitle="Espacios luminosos, seguros y diseñados para el aprendizaje activo."
                breadcrumbs={[
                    { label: 'Nosotros', href: route('public.nosotros') },
                    { label: 'Infraestructura' },
                ]}
            />
            <InfrastructureSection />
            <InstitutionalCTA primaryLabel="Agendar visita" primaryHref={route('public.contacto')} />
        </PublicLayout>
    );
}
