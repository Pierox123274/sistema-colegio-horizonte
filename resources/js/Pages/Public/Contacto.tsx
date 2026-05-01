import { CTASection } from '@/Components/Public/CTASection';
import PublicLayout from '@/Layouts/PublicLayout';
import { Mail, MapPin, Phone } from 'lucide-react';

export default function Contacto() {
    return (
        <PublicLayout
            title="Contacto — I.E.P. Horizonte"
            description="Ubicación y datos de contacto (demo). Formulario próximamente."
        >
            <div className="border-b border-plomo/10 bg-gradient-to-b from-navy-50 to-white py-12 sm:py-16">
                <div className="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                    <h1 className="text-4xl font-bold tracking-tight text-navy-900 sm:text-5xl">
                        Contacto
                    </h1>
                    <p className="mx-auto mt-4 max-w-2xl text-plomo">
                        Escríbenos o visítanos. El formulario web se activará en
                        una fase futura; por ahora usa los canales demo.
                    </p>
                </div>
            </div>

            <section className="py-16 sm:py-20">
                <div className="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
                    <div className="rounded-2xl border border-plomo/10 bg-white p-8 shadow-sm">
                        <h2 className="text-lg font-bold text-navy-900">
                            Datos de contacto (demo)
                        </h2>
                        <ul className="mt-8 space-y-6 text-plomo">
                            <li className="flex gap-4">
                                <MapPin className="h-6 w-6 shrink-0 text-brand-red" />
                                <span>
                                    Av. Institucional 123, distrito educativo,
                                    Lima — Perú
                                </span>
                            </li>
                            <li className="flex gap-4">
                                <Phone className="h-6 w-6 shrink-0 text-brand-red" />
                                <span>+51 1 234 5678 (central)</span>
                            </li>
                            <li className="flex gap-4">
                                <Mail className="h-6 w-6 shrink-0 text-brand-red" />
                                <span>contacto@horizonte.edu.pe</span>
                            </li>
                        </ul>
                    </div>
                    <div className="rounded-2xl border border-dashed border-plomo/25 bg-navy-50/50 p-8">
                        <h2 className="text-lg font-bold text-navy-900">
                            Formulario
                        </h2>
                        <p className="mt-4 text-sm leading-relaxed text-plomo">
                            Próximamente podrás enviar consultas desde esta página.
                            Mientras tanto, utiliza el correo o teléfono
                            indicados.
                        </p>
                        <div className="mt-8 h-40 rounded-xl bg-gradient-to-br from-navy-100 to-white ring-1 ring-plomo/10" />
                    </div>
                </div>
            </section>

            <CTASection />
        </PublicLayout>
    );
}
