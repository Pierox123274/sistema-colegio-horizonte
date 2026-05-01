import { NewsSection } from '@/Components/Public/NewsSection';
import { CTASection } from '@/Components/Public/CTASection';
import PublicLayout from '@/Layouts/PublicLayout';

export default function Noticias() {
    return (
        <PublicLayout
            title="Noticias — I.E.P. Horizonte"
            description="Novedades y comunicados institucionales (demo)."
        >
            <div className="border-b border-plomo/10 bg-gradient-to-b from-navy-50 to-white py-12 sm:py-16">
                <div className="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                    <h1 className="text-4xl font-bold tracking-tight text-navy-900 sm:text-5xl">
                        Noticias
                    </h1>
                    <p className="mx-auto mt-4 max-w-2xl text-plomo">
                        Contenido de demostración. La gestión editorial se
                        conectará a base de datos en el roadmap.
                    </p>
                </div>
            </div>
            <NewsSection />
            <CTASection />
        </PublicLayout>
    );
}
