import { EducationalLevelsSection } from '@/Components/Public/EducationalLevelsSection';
import { ProposalSection } from '@/Components/Public/ProposalSection';
import { CTASection } from '@/Components/Public/CTASection';
import PublicLayout from '@/Layouts/PublicLayout';

export default function Niveles() {
    return (
        <PublicLayout
            title="Niveles educativos — I.E.P. Horizonte"
            description="Inicial, Primaria y Secundaria. Propuesta por etapa."
        >
            <div className="border-b border-plomo/10 bg-gradient-to-b from-navy-50 to-white py-12 sm:py-16">
                <div className="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                    <h1 className="text-4xl font-bold tracking-tight text-navy-900 sm:text-5xl">
                        Niveles educativos
                    </h1>
                    <p className="mx-auto mt-4 max-w-2xl text-plomo">
                        Tres etapas con continuidad pedagógica y acompañamiento
                        personalizado.
                    </p>
                </div>
            </div>
            <EducationalLevelsSection showLinkToFull={false} />
            <ProposalSection />
            <CTASection />
        </PublicLayout>
    );
}
