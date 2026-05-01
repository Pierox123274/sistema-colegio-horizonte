import { AboutSection } from '@/Components/Public/AboutSection';
import { CTASection } from '@/Components/Public/CTASection';
import PublicLayout from '@/Layouts/PublicLayout';

export default function Nosotros() {
    return (
        <PublicLayout
            title="Nosotros — I.E.P. Horizonte"
            description="Historia, misión y comunidad educativa del I.E.P. Horizonte."
        >
            <div className="border-b border-plomo/10 bg-gradient-to-b from-navy-50 to-white py-12 sm:py-16">
                <div className="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                    <h1 className="text-4xl font-bold tracking-tight text-navy-900 sm:text-5xl">
                        Nosotros
                    </h1>
                    <p className="mx-auto mt-4 max-w-2xl text-plomo">
                        Conoce nuestra historia, equipo y el ambiente que hace
                        única la experiencia Horizonte.
                    </p>
                </div>
            </div>
            <AboutSection />
            <CTASection />
        </PublicLayout>
    );
}
