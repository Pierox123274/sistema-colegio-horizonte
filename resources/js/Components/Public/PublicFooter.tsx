import { Link } from '@inertiajs/react';

export function PublicFooter() {
    return (
        <footer className="border-t border-navy-900/10 bg-navy-950 text-white">
            <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div className="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <div className="flex items-center gap-2">
                            <span className="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-yellow font-serif text-lg font-bold text-navy-950">
                                H
                            </span>
                            <span className="font-bold">I.E.P. Horizonte</span>
                        </div>
                        <p className="mt-4 text-sm leading-relaxed text-white/70">
                            Formación integral con visión de futuro. Inicial,
                            Primaria y Secundaria en un ambiente seguro y
                            cercano.
                        </p>
                    </div>
                    <div>
                        <h3 className="text-xs font-bold uppercase tracking-wider text-brand-yellow">
                            Enlaces
                        </h3>
                        <ul className="mt-4 space-y-2 text-sm text-white/80">
                            <li>
                                <Link
                                    href={route('public.nosotros')}
                                    className="transition hover:text-white"
                                >
                                    Nosotros
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href={route('public.niveles')}
                                    className="transition hover:text-white"
                                >
                                    Niveles educativos
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href={route('public.admision')}
                                    className="transition hover:text-white"
                                >
                                    Admisión
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href={route('public.contacto')}
                                    className="transition hover:text-white"
                                >
                                    Contacto
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 className="text-xs font-bold uppercase tracking-wider text-brand-yellow">
                            Contacto (demo)
                        </h3>
                        <ul className="mt-4 space-y-2 text-sm text-white/80">
                            <li>Av. Institucional 123, Lima</li>
                            <li>+51 1 234 5678</li>
                            <li>admisión@horizonte.edu.pe</li>
                        </ul>
                    </div>
                    <div>
                        <h3 className="text-xs font-bold uppercase tracking-wider text-brand-yellow">
                            Intranet
                        </h3>
                        <p className="mt-4 text-sm text-white/70">
                            Docentes, familias y personal acceden al portal
                            privado.
                        </p>
                        <Link
                            href={route('login')}
                            className="mt-4 inline-block rounded-lg border border-white/20 px-4 py-2 text-sm font-semibold text-white transition hover:border-brand-yellow hover:text-brand-yellow"
                        >
                            Iniciar sesión
                        </Link>
                    </div>
                </div>
                <div className="mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-8 text-xs text-white/50 sm:flex-row">
                    <p>
                        © {new Date().getFullYear()} I.E.P. Horizonte. Todos los
                        derechos reservados.
                    </p>
                    <p className="text-center sm:text-right">
                        Contenido institucional de demostración — Fase 4
                    </p>
                </div>
            </div>
        </footer>
    );
}
