import { Link } from '@inertiajs/react';
import { Clock, Globe, Mail, MapPin, Phone, Share2 } from 'lucide-react';

const quickLinks = [
    { label: 'Presentación', href: route('public.nosotros') },
    { label: 'Historia', href: route('public.nosotros.historia') },
    { label: 'Misión y visión', href: route('public.nosotros.mision-vision') },
    { label: 'Valores', href: route('public.nosotros.valores') },
    { label: 'Infraestructura', href: route('public.nosotros.infraestructura') },
];

const nivelesLinks = [
    { label: 'Inicial', href: route('public.niveles.inicial') },
    { label: 'Primaria', href: route('public.niveles.primaria') },
    { label: 'Secundaria', href: route('public.niveles.secundaria') },
    { label: 'Todos los niveles', href: route('public.niveles') },
];

const admisionLinks = [
    { label: 'Admisión 2026', href: route('public.admision') },
    { label: 'Requisitos', href: route('public.admision.requisitos') },
    { label: 'Matrícula', href: route('public.admision.matricula') },
    { label: 'Contacto', href: route('public.contacto') },
];

const vidaLinks = [
    { label: 'Vida escolar', href: route('public.vida-escolar') },
    { label: 'Actividades', href: route('public.vida-escolar.actividades') },
    { label: 'Talleres', href: route('public.vida-escolar.talleres') },
    { label: 'Eventos', href: route('public.vida-escolar.eventos') },
    { label: 'Galería', href: route('public.galeria') },
    { label: 'Noticias', href: route('public.noticias') },
];

const social = [
    { icon: Share2, label: 'Facebook', href: '#' },
    { icon: Globe, label: 'Instagram', href: '#' },
    { icon: Share2, label: 'YouTube', href: '#' },
    { icon: Globe, label: 'LinkedIn', href: '#' },
];

function FooterLinkList({ title, links }: { title: string; links: { label: string; href: string }[] }) {
    return (
        <div>
            <h3 className="text-xs font-bold uppercase tracking-[0.15em] text-institutional-gold">
                {title}
            </h3>
            <ul className="mt-4 space-y-2.5 text-sm text-white/75">
                {links.map((l) => (
                    <li key={l.label}>
                        <Link href={l.href} className="transition hover:text-institutional-gold-light">
                            {l.label}
                        </Link>
                    </li>
                ))}
            </ul>
        </div>
    );
}

export function PublicFooter() {
    return (
        <footer className="border-t border-white/10 bg-gradient-to-b from-institutional-blue-950 to-institutional-blue-900 text-white">
            <div className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div className="grid gap-12 lg:grid-cols-12">
                    <div className="lg:col-span-4">
                        <div className="flex items-center gap-3">
                            <span className="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-institutional-gold to-institutional-gold-light font-display text-lg font-bold text-institutional-blue-950 shadow-md">
                                H
                            </span>
                            <div>
                                <p className="font-display text-xl font-bold">I.E.P. Horizonte</p>
                                <p className="text-xs uppercase tracking-[0.2em] text-institutional-gold-light">
                                    Excelencia con valores
                                </p>
                            </div>
                        </div>
                        <p className="mt-5 max-w-sm text-sm leading-relaxed text-white/72">
                            Institución educativa privada en Inicial, Primaria y Secundaria. Más de
                            quince años formando personas íntegras, competentes y comprometidas con
                            su entorno.
                        </p>
                        <div className="mt-6 flex gap-2">
                            {social.map(({ icon: Icon, label, href }) => (
                                <a
                                    key={label}
                                    href={href}
                                    aria-label={label}
                                    className="flex h-10 w-10 items-center justify-center rounded-xl border border-white/15 bg-white/5 text-white/80 transition hover:border-institutional-gold/40 hover:bg-institutional-gold/15 hover:text-institutional-gold"
                                >
                                    <Icon className="h-4 w-4" />
                                </a>
                            ))}
                        </div>
                    </div>

                    <div className="grid gap-10 sm:grid-cols-2 lg:col-span-5 lg:grid-cols-3">
                        <FooterLinkList title="Institución" links={quickLinks} />
                        <FooterLinkList title="Niveles" links={nivelesLinks} />
                        <FooterLinkList title="Admisión" links={admisionLinks} />
                    </div>

                    <div className="lg:col-span-3">
                        <FooterLinkList title="Vida escolar" links={vidaLinks} />
                        <Link
                            href={route('login')}
                            className="mt-4 inline-block text-sm font-semibold text-institutional-gold-light hover:underline"
                        >
                            Portal institucional →
                        </Link>
                    </div>
                </div>

                <div className="mt-14 grid gap-10 border-t border-white/10 pt-14 lg:grid-cols-2">
                    <div>
                        <h3 className="text-xs font-bold uppercase tracking-[0.15em] text-institutional-gold">
                            Contacto
                        </h3>
                        <ul className="mt-4 space-y-3 text-sm text-white/75">
                            <li className="flex items-start gap-2.5">
                                <MapPin className="mt-0.5 h-4 w-4 shrink-0 text-institutional-gold" />
                                Av. Institucional 123, San Isidro, Lima
                            </li>
                            <li className="flex items-center gap-2.5">
                                <Phone className="h-4 w-4 text-institutional-gold" />
                                +51 1 234 5678
                            </li>
                            <li className="flex items-center gap-2.5">
                                <Mail className="h-4 w-4 text-institutional-gold" />
                                admision@horizonte.edu.pe
                            </li>
                            <li className="flex items-start gap-2.5">
                                <Clock className="mt-0.5 h-4 w-4 shrink-0 text-institutional-gold" />
                                <span>
                                    Lunes a viernes: 7:30 – 16:30
                                    <br />
                                    Sábados (admisión): 9:00 – 13:00
                                </span>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 className="text-xs font-bold uppercase tracking-[0.15em] text-institutional-gold">
                            Ubicación
                        </h3>
                        <div className="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                            <div className="flex aspect-[16/9] items-center justify-center bg-gradient-to-br from-institutional-blue-800/80 to-institutional-blue-900/90 p-6 text-center">
                                <div>
                                    <MapPin className="mx-auto h-8 w-8 text-institutional-gold/80" />
                                    <p className="mt-3 text-sm font-medium text-white/80">
                                        Campus Horizonte
                                    </p>
                                    <p className="mt-1 text-xs text-white/50">
                                        Mapa interactivo (Google Maps — demo)
                                    </p>
                                </div>
                            </div>
                        </div>
                        <Link
                            href={route('public.contacto')}
                            className="mt-3 inline-block text-sm font-medium text-institutional-gold-light hover:underline"
                        >
                            Cómo llegar y formulario →
                        </Link>
                    </div>
                </div>

                <div className="mt-14 flex flex-col items-center justify-between gap-3 border-t border-white/10 pt-8 text-xs text-white/45 sm:flex-row">
                    <p>© {new Date().getFullYear()} I.E.P. Horizonte. Todos los derechos reservados.</p>
                    <p className="text-white/35">Contenido institucional de demostración</p>
                </div>
            </div>
        </footer>
    );
}
