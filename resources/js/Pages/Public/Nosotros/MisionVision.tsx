import { MissionVisionSection } from '@/Components/Public/Institutional/MissionVisionSection';
import { PageHero } from '@/Components/Public/ui/PageHero';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

export default function MisionVision() {
    return (
        <PublicLayout title="Misión y visión — I.E.P. Horizonte" description="Propósito institucional.">
            <PageHero
                title="Misión y visión"
                subtitle="El horizonte que guía cada decisión pedagógica y de gestión."
                breadcrumbs={[
                    { label: 'Nosotros', href: route('public.nosotros') },
                    { label: 'Misión y visión' },
                ]}
            />
            <MissionVisionSection />
            <InstitutionalCTA />
        </PublicLayout>
    );
}
