import { Link, usePage } from '@inertiajs/react';
import { Menu, X } from 'lucide-react';
import { useState } from 'react';
import type { PageProps } from '@/types';

const navLinks = [
    { label: 'Inicio', routeName: 'public.home' as const },
    { label: 'Nosotros', routeName: 'public.nosotros' as const },
    { label: 'Niveles', routeName: 'public.niveles' as const },
    { label: 'Admisión', routeName: 'public.admision' as const },
    { label: 'Noticias', routeName: 'public.noticias' as const },
    { label: 'Contacto', routeName: 'public.contacto' as const },
];

export function PublicNavbar() {
    const { canLogin = false, canRegister = false } = usePage<PageProps>().props;
    const [open, setOpen] = useState(false);

    return (
        <header className="sticky top-0 z-50 border-b border-plomo/10 bg-white/90 shadow-sm shadow-navy-900/5 backdrop-blur-md">
            <div className="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <Link
                    href={route('public.home')}
                    className="flex items-center gap-2 transition-opacity hover:opacity-90"
                >
                    <span className="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-yellow font-serif text-lg font-bold text-navy-950 shadow-sm">
                        H
                    </span>
                    <span className="hidden flex-col leading-tight sm:flex">
                        <span className="text-sm font-bold tracking-tight text-navy-900">
                            I.E.P. Horizonte
                        </span>
                        <span className="text-[10px] font-semibold uppercase tracking-widest text-brand-red">
                            Colegio privado
                        </span>
                    </span>
                </Link>

                <nav className="hidden items-center gap-1 lg:flex">
                    {navLinks.map((item) => (
                        <Link
                            key={item.routeName}
                            href={route(item.routeName)}
                            className={`rounded-lg px-3 py-2 text-sm font-medium transition-colors ${
                                route().current(item.routeName)
                                    ? 'bg-navy-900 text-white'
                                    : 'text-plomo hover:bg-navy-50 hover:text-navy-900'
                            }`}
                        >
                            {item.label}
                        </Link>
                    ))}
                </nav>

                <div className="hidden items-center gap-2 lg:flex">
                    {canRegister && (
                        <Link
                            href={route('register')}
                            className="rounded-lg px-3 py-2 text-sm font-medium text-navy-900 hover:bg-navy-50"
                        >
                            Registro
                        </Link>
                    )}
                    {canLogin && (
                        <Link
                            href={route('login')}
                            className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:bg-navy-950"
                        >
                            Intranet
                        </Link>
                    )}
                </div>

                <button
                    type="button"
                    className="inline-flex rounded-lg border border-plomo/15 p-2 text-navy-900 lg:hidden"
                    onClick={() => setOpen((v) => !v)}
                    aria-expanded={open}
                    aria-label={open ? 'Cerrar menú' : 'Abrir menú'}
                >
                    {open ? (
                        <X className="h-6 w-6" />
                    ) : (
                        <Menu className="h-6 w-6" />
                    )}
                </button>
            </div>

            <div
                className={`border-t border-plomo/10 bg-white lg:hidden ${open ? 'max-h-[32rem] opacity-100' : 'max-h-0 overflow-hidden opacity-0'} transition-all duration-300 ease-out`}
            >
                <nav className="flex flex-col gap-1 px-4 py-4">
                    {navLinks.map((item) => (
                        <Link
                            key={item.routeName}
                            href={route(item.routeName)}
                            className="rounded-lg px-3 py-2.5 text-sm font-medium text-navy-900 hover:bg-navy-50"
                            onClick={() => setOpen(false)}
                        >
                            {item.label}
                        </Link>
                    ))}
                    <div className="mt-2 flex flex-col gap-2 border-t border-plomo/10 pt-4">
                        {canLogin && (
                            <Link
                                href={route('login')}
                                className="rounded-lg bg-navy-900 py-2.5 text-center text-sm font-semibold text-white"
                                onClick={() => setOpen(false)}
                            >
                                Acceso intranet
                            </Link>
                        )}
                        {canRegister && (
                            <Link
                                href={route('register')}
                                className="rounded-lg border border-plomo/20 py-2.5 text-center text-sm font-medium text-navy-900"
                                onClick={() => setOpen(false)}
                            >
                                Registro
                            </Link>
                        )}
                    </div>
                </nav>
            </div>
        </header>
    );
}
