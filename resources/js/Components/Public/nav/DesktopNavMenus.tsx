import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ChevronDown } from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';
import { usePublicTheme } from '@/Components/Public/Premium/PublicThemeProvider';
import {
    AdmisionMenuPanel,
    NosotrosMenuPanel,
    NivelesMenuPanel,
    PortalMenuPanel,
    VidaEscolarMenuPanel,
} from './NavMenuPanels';
import { NavDropdownStylesProvider } from './NavDropdownStylesContext';
import { getNavDropdownStyles } from './navDropdownStyles';

const CLOSE_DELAY_MS = 120;

export type MenuId = 'nosotros' | 'niveles' | 'admision' | 'vida' | 'portal';

type DesktopNavMenusProps = {
    url: string;
    canLogin: boolean;
    onHome: boolean;
    linkClass: (routeActive: boolean, menuOpen?: boolean) => string;
};

function pathActive(prefix: string, url: string): boolean {
    return url === prefix || url.startsWith(`${prefix}/`);
}

const PANEL_ALIGN: Record<MenuId, string> = {
    nosotros: 'left-0',
    niveles: 'left-1/2 -translate-x-1/2',
    admision: 'left-1/2 -translate-x-1/2',
    vida: 'right-0',
    portal: 'right-0',
};

function renderPanel(id: MenuId) {
    switch (id) {
        case 'nosotros':
            return <NosotrosMenuPanel />;
        case 'niveles':
            return <NivelesMenuPanel />;
        case 'admision':
            return <AdmisionMenuPanel />;
        case 'vida':
            return <VidaEscolarMenuPanel />;
        case 'portal':
            return <PortalMenuPanel />;
    }
}

export function DesktopNavMenus({ url, canLogin, onHome, linkClass }: DesktopNavMenusProps) {
    const { isDark } = usePublicTheme();
    const dropdownStyles = getNavDropdownStyles(isDark);
    const [activeMenu, setActiveMenu] = useState<MenuId | null>(null);
    const closeTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

    const cancelClose = useCallback(() => {
        if (closeTimer.current) {
            clearTimeout(closeTimer.current);
            closeTimer.current = null;
        }
    }, []);

    const openMenu = useCallback(
        (id: MenuId) => {
            cancelClose();
            setActiveMenu(id);
        },
        [cancelClose],
    );

    const scheduleClose = useCallback(() => {
        cancelClose();
        closeTimer.current = setTimeout(() => setActiveMenu(null), CLOSE_DELAY_MS);
    }, [cancelClose]);

    const closeNow = useCallback(() => {
        cancelClose();
        setActiveMenu(null);
    }, [cancelClose]);

    useEffect(() => {
        const onKey = (e: KeyboardEvent) => {
            if (e.key === 'Escape') {
                closeNow();
            }
        };
        document.addEventListener('keydown', onKey);
        return () => document.removeEventListener('keydown', onKey);
    }, [closeNow]);

    const menuTriggerClass = (id: MenuId, routeActive: boolean) =>
        linkClass(routeActive, activeMenu === id);

    const menus: { id: MenuId; label: string; routeActive: boolean }[] = [
        { id: 'nosotros', label: 'Nosotros', routeActive: pathActive('/nosotros', url) },
        { id: 'niveles', label: 'Niveles', routeActive: pathActive('/niveles', url) },
        { id: 'admision', label: 'Admisión', routeActive: pathActive('/admision', url) },
        {
            id: 'vida',
            label: 'Vida escolar',
            routeActive: pathActive('/vida-escolar', url) || pathActive('/galeria', url),
        },
    ];

    return (
        <nav
            className="relative hidden items-center gap-0.5 xl:flex"
            aria-label="Principal"
            onMouseLeave={scheduleClose}
        >
            <Link href={route('public.home')} className={linkClass(onHome)}>
                Inicio
            </Link>

            {menus.map(({ id, label, routeActive }) => (
                <button
                    key={id}
                    type="button"
                    className={menuTriggerClass(id, routeActive)}
                    aria-expanded={activeMenu === id}
                    aria-haspopup="true"
                    onMouseEnter={() => openMenu(id)}
                    onFocus={() => openMenu(id)}
                >
                    {label}
                    <ChevronDown
                        className={`h-3.5 w-3.5 transition-transform duration-150 ${activeMenu === id ? 'rotate-180' : ''}`}
                    />
                </button>
            ))}

            <Link href={route('public.noticias')} className={linkClass(pathActive('/noticias', url))}>
                Noticias
            </Link>
            <Link href={route('public.contacto')} className={linkClass(pathActive('/contacto', url))}>
                Contacto
            </Link>

            {canLogin ? (
                <button
                    type="button"
                    className={menuTriggerClass('portal', false)}
                    aria-expanded={activeMenu === 'portal'}
                    aria-haspopup="true"
                    onMouseEnter={() => openMenu('portal')}
                    onFocus={() => openMenu('portal')}
                >
                    Portal
                    <ChevronDown
                        className={`h-3.5 w-3.5 transition-transform duration-150 ${activeMenu === 'portal' ? 'rotate-180' : ''}`}
                    />
                </button>
            ) : null}

            {activeMenu ? (
                <div
                    className={`absolute top-full z-[70] pt-2 ${PANEL_ALIGN[activeMenu]}`}
                    onMouseEnter={cancelClose}
                    role="menu"
                >
                    <NavDropdownStylesProvider isDark={isDark}>
                        <motion.div
                            key={activeMenu}
                            initial={{ opacity: 0, y: 4 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.12, ease: 'easeOut' }}
                            className={dropdownStyles.panel}
                        >
                            {renderPanel(activeMenu)}
                        </motion.div>
                    </NavDropdownStylesProvider>
                </div>
            ) : null}
        </nav>
    );
}
