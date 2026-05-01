import { AboutSection } from '@/Components/Public/AboutSection';
import { AdmissionSection } from '@/Components/Public/AdmissionSection';
import { CTASection } from '@/Components/Public/CTASection';
import { EducationalLevelsSection } from '@/Components/Public/EducationalLevelsSection';
import { HeroSection } from '@/Components/Public/HeroSection';
import { NewsSection } from '@/Components/Public/NewsSection';
import { ProposalSection } from '@/Components/Public/ProposalSection';
import PublicLayout from '@/Layouts/PublicLayout';

export default function Home() {
    return (
        <PublicLayout
            title="Inicio — I.E.P. Horizonte"
            description="Colegio privado: Inicial, Primaria y Secundaria. Admisión, propuesta educativa e intranet."
        >
            <HeroSection />
            <AboutSection condensed />
            <EducationalLevelsSection />
            <ProposalSection />
            <AdmissionSection condensed />
            <NewsSection limit={3} />
            <CTASection />
        </PublicLayout>
    );
}
