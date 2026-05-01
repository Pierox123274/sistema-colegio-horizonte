import { AdmissionSection } from '@/Components/Public/AdmissionSection';
import { CTASection } from '@/Components/Public/CTASection';
import PublicLayout from '@/Layouts/PublicLayout';

export default function Admision() {
    return (
        <PublicLayout
            title="Admisión — I.E.P. Horizonte"
            description="Proceso de admisión, fechas y requisitos (información demo)."
        >
            <div className="border-b border-plomo/10 bg-gradient-to-b from-navy-50 to-white py-12 sm:py-16">
                <div className="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                    <h1 className="text-4xl font-bold tracking-tight text-navy-900 sm:text-5xl">
                        Admisión
                    </h1>
                    <p className="mx-auto mt-4 max-w-2xl text-plomo">
                        Te guiamos en cada etapa. Los formularios en línea se
                        habilitarán en una fase posterior del proyecto.
                    </p>
                </div>
            </div>
            <AdmissionSection />
            <CTASection />
        </PublicLayout>
    );
}
