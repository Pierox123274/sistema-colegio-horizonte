import { NewsMagazineSection } from '@/Components/Public/Premium/NewsMagazineSection';
import { PublicPageHeroImage } from '@/Components/Public/Premium/PublicHeroImage';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

export default function NoticiasIndex() {
    return (
        <PublicLayout title="Noticias — I.E.P. Horizonte" description="Comunicados institucionales.">
            <PublicPageHeroImage
                title="Noticias"
                subtitle="Novedades, logros y comunicados de la comunidad Horizonte."
                imageKey="newsFeria"
                breadcrumbs={[{ label: 'Noticias' }]}
            />
            <NewsMagazineSection showHeader={false} />
            <InstitutionalCTA />
        </PublicLayout>
    );
}
