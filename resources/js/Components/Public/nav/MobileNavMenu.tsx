import { AnimatePresence, motion } from 'framer-motion';
import { Link } from '@inertiajs/react';
import { ChevronDown, LogIn, X } from 'lucide-react';
import { useState } from 'react';
import {
    admisionMenu,
    navHref,
    nosotrosMenu,
    nivelesMenu,
    portalMenu,
    vidaEscolarMenu,
} from './publicNavConfig';

type MobileNavMenuProps = {
    open: boolean;
    onClose: () => void;
    canLogin: boolean;
};

function MobileAccordion({ title, children }: { title: string; children: React.ReactNode }) {
    const [expanded, setExpanded] = useState(false);
    return (
        <div className="border-b border-white/10">
            <button
                type="button"
                className="flex w-full items-center justify-between py-4 text-left text-base font-semibold text-white"
                onClick={() => setExpanded((v) => !v)}
                aria-expanded={expanded}
            >
                {title}
                <ChevronDown className={`h-5 w-5 transition ${expanded ? 'rotate-180' : ''}`} />
            </button>
            <AnimatePresence initial={false}>
                {expanded ? (
                    <motion.div
                        initial={{ height: 0, opacity: 0 }}
                        animate={{ height: 'auto', opacity: 1 }}
                        exit={{ height: 0, opacity: 0 }}
                        className="overflow-hidden pb-3"
                    >
                        {children}
                    </motion.div>
                ) : null}
            </AnimatePresence>
        </div>
    );
}

export function MobileNavMenu({ open, onClose, canLogin }: MobileNavMenuProps) {
    return (
        <AnimatePresence>
            {open ? (
                <motion.div
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    exit={{ opacity: 0 }}
                    className="fixed inset-0 z-[60] xl:hidden"
                    role="dialog"
                    aria-modal="true"
                    aria-label="Menú de navegación"
                >
                    <div
                        className="absolute inset-0 bg-institutional-blue-950/80 backdrop-blur-sm"
                        role="button"
                        tabIndex={0}
                        aria-label="Cerrar menú"
                        onClick={onClose}
                        onKeyDown={(event) => {
                            if (event.key === 'Escape' || event.key === 'Enter' || event.key === ' ') {
                                event.preventDefault();
                                onClose();
                            }
                        }}
                    />
                    <motion.nav
                        initial={{ x: '100%' }}
                        animate={{ x: 0 }}
                        exit={{ x: '100%' }}
                        transition={{ type: 'spring', damping: 28, stiffness: 280 }}
                        className="absolute inset-y-0 right-0 flex w-full max-w-sm flex-col bg-gradient-to-b from-institutional-blue-950 via-institutional-blue-900 to-institutional-blue-950"
                    >
                        <div className="flex items-center justify-between border-b border-white/10 px-5 py-4">
                            <span className="font-display text-lg font-bold text-white">Menú</span>
                            <button
                                type="button"
                                onClick={onClose}
                                className="rounded-xl p-2 text-white/80 hover:bg-white/10"
                                aria-label="Cerrar menú"
                            >
                                <X className="h-6 w-6" />
                            </button>
                        </div>
                        <div className="flex-1 overflow-y-auto px-5 py-2">
                            <Link href={route('public.home')} onClick={onClose} className="block py-4 font-semibold text-white">
                                Inicio
                            </Link>
                            <MobileAccordion title="Nosotros">
                                <ul className="space-y-1 pl-1">
                                    {nosotrosMenu.map((item) => (
                                        <li key={item.routeName}>
                                            <Link
                                                href={navHref(item)}
                                                onClick={onClose}
                                                className="block rounded-lg px-3 py-2.5 text-sm text-white/85 hover:bg-white/10"
                                            >
                                                {item.label}
                                            </Link>
                                        </li>
                                    ))}
                                </ul>
                            </MobileAccordion>
                            <MobileAccordion title="Niveles">
                                <ul className="space-y-1">
                                    {nivelesMenu.map((l) => (
                                        <li key={l.key}>
                                            <Link
                                                href={navHref(l)}
                                                onClick={onClose}
                                                className="block rounded-lg px-3 py-2.5 text-sm text-white/85 hover:bg-white/10"
                                            >
                                                {l.label} — {l.grades}
                                            </Link>
                                        </li>
                                    ))}
                                </ul>
                            </MobileAccordion>
                            <MobileAccordion title="Admisión">
                                <ul className="space-y-1">
                                    {admisionMenu.map((item) => (
                                        <li key={item.routeName}>
                                            <Link
                                                href={navHref(item)}
                                                onClick={onClose}
                                                className="block rounded-lg px-3 py-2.5 text-sm text-white/85 hover:bg-white/10"
                                            >
                                                {item.label}
                                            </Link>
                                        </li>
                                    ))}
                                </ul>
                            </MobileAccordion>
                            <MobileAccordion title="Vida escolar">
                                <ul className="space-y-1">
                                    {vidaEscolarMenu.map((item) => (
                                        <li key={item.routeName}>
                                            <Link
                                                href={navHref(item)}
                                                onClick={onClose}
                                                className="block rounded-lg px-3 py-2.5 text-sm text-white/85 hover:bg-white/10"
                                            >
                                                {item.label}
                                            </Link>
                                        </li>
                                    ))}
                                </ul>
                            </MobileAccordion>
                            <Link href={route('public.noticias')} onClick={onClose} className="block border-b border-white/10 py-4 font-semibold text-white">
                                Noticias
                            </Link>
                            <Link href={route('public.contacto')} onClick={onClose} className="block py-4 font-semibold text-white">
                                Contacto
                            </Link>
                            {canLogin ? (
                                <MobileAccordion title="Portal">
                                    <ul className="space-y-1">
                                        {portalMenu.map((item) => (
                                            <li key={item.label}>
                                                <Link
                                                    href={navHref(item)}
                                                    onClick={onClose}
                                                    className="block rounded-lg px-3 py-2.5 text-sm text-white/85 hover:bg-white/10"
                                                >
                                                    {item.label}
                                                </Link>
                                            </li>
                                        ))}
                                    </ul>
                                </MobileAccordion>
                            ) : null}
                        </div>
                        {canLogin ? (
                            <div className="border-t border-white/10 p-5">
                                <Link
                                    href={route('login')}
                                    onClick={onClose}
                                    className="flex w-full items-center justify-center gap-2 rounded-2xl bg-institutional-gold py-4 text-sm font-bold text-institutional-blue-950"
                                >
                                    <LogIn className="h-4 w-4" />
                                    Portal institucional
                                </Link>
                            </div>
                        ) : null}
                    </motion.nav>
                </motion.div>
            ) : null}
        </AnimatePresence>
    );
}
