import { SchoolLifeSection } from '@/Components/Public/Institutional/SchoolLifeSection';
import { PageHero } from '@/Components/Public/ui/PageHero';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

export default function VidaEscolarIndex() {
    return (
        <PublicLayout title="Vida escolar — I.E.P. Horizonte" description="Deportes y comunidad.">
            <PageHero
                title="Vida escolar"
                subtitle="Deporte, convivencia y experiencias que complementan la formación académica."
                breadcrumbs={[{ label: 'Vida escolar' }]}
            />
            <SchoolLifeSection />
            <InstitutionalCTA secondaryLabel="Ver galería" secondaryHref={route('public.galeria')} />
        </PublicLayout>
    );
}
