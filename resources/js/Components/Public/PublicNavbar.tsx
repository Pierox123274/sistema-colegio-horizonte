import { Link, usePage } from '@inertiajs/react';
import { LogIn, Menu, Moon, Sun } from 'lucide-react';
import { useEffect, useState } from 'react';
import type { PageProps } from '@/types';
import type { CmsSettings } from '@/types/cms';
import { usePublicTheme } from '@/Components/Public/Premium/PublicThemeProvider';
import { DesktopNavMenus } from '@/Components/Public/nav/DesktopNavMenus';
import { MobileNavMenu } from '@/Components/Public/nav/MobileNavMenu';

type NavbarProps = PageProps & { cmsSettings?: CmsSettings };

export function PublicNavbar() {
    const { canLogin = false, cmsSettings } = usePage<NavbarProps>().props;
    const schoolName = cmsSettings?.schoolName ?? 'I.E.P. Horizonte';
    const tagline = cmsSettings?.schoolTagline || 'Excelencia educativa';
    const logoUrl = cmsSettings?.logoUrl;
    const { url } = usePage();
    const { isDark, toggleTheme } = usePublicTheme();
    const [mobileOpen, setMobileOpen] = useState(false);
    const [scrolled, setScrolled] = useState(false);

    const onHome = url === '/' || url === '';
    const lightOnHero = onHome && !scrolled && !isDark;

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 24);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    useEffect(() => {
        document.body.style.overflow = mobileOpen ? 'hidden' : '';
        return () => {
            document.body.style.overflow = '';
        };
    }, [mobileOpen]);

    const navSurface = scrolled
        ? 'border-slate-200/80 bg-white/80 shadow-sm backdrop-blur-xl dark:border-white/10 dark:bg-[#071526]/80 dark:shadow-lg'
        : lightOnHero
          ? 'border-transparent bg-gradient-to-b from-[#071526]/60 to-transparent'
          : 'border-slate-200/70 bg-white/75 backdrop-blur-xl dark:border-white/10 dark:bg-[#071526]/70';

    const linkClass = (routeActive: boolean, menuOpen = false) => {
        const base =
            'relative inline-flex items-center gap-1 rounded-xl px-3 py-2 text-sm font-medium transition-colors duration-150';
        if (lightOnHero) {
            return `${base} ${
                routeActive || menuOpen
                    ? 'bg-white/15 text-white'
                    : 'text-white/90 hover:bg-white/10 hover:text-white'
            }`;
        }
        return `${base} ${
            routeActive || menuOpen
                ? 'bg-slate-100 text-slate-900 dark:bg-white/10 dark:text-amber-400'
                : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-200 dark:hover:bg-white/10 dark:hover:text-white'
        }`;
    };

    const iconBtnClass = lightOnHero
        ? 'rounded-xl p-2.5 text-white/90 transition hover:bg-white/10 hover:text-white'
        : 'rounded-xl p-2.5 text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-white/10 dark:hover:text-white';

    return (
        <>
            <header
                className={`sticky top-0 z-50 border-b transition-all duration-300 ${navSurface} ${scrolled ? 'h-[4.25rem]' : 'h-[4.75rem]'}`}
            >
                <div className="mx-auto flex h-full max-w-7xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                    <Link href={route('public.home')} className="group flex shrink-0 items-center gap-2.5">
                        {logoUrl ? (
                            <img
                                src={logoUrl}
                                alt={schoolName}
                                className="h-11 w-auto max-w-[8rem] rounded-lg object-contain"
                            />
                        ) : (
                            <span className="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-amber-300 font-display text-lg font-extrabold text-[#071526] shadow-md transition group-hover:scale-[1.02]">
                                H
                            </span>
                        )}
                        <span className="hidden flex-col leading-tight sm:flex">
                            <span
                                className={`font-display text-sm font-bold ${lightOnHero ? 'text-white' : 'text-slate-900 dark:text-white'}`}
                            >
                                {schoolName}
                            </span>
                            <span
                                className={`text-[10px] font-semibold uppercase tracking-[0.2em] ${lightOnHero ? 'text-amber-300' : 'text-amber-500 dark:text-amber-400'}`}
                            >
                                {tagline}
                            </span>
                        </span>
                    </Link>

                    <DesktopNavMenus url={url} canLogin={canLogin} onHome={onHome} linkClass={linkClass} />

                    <div className="hidden items-center gap-2 lg:flex">
                        <button
                            type="button"
                            onClick={toggleTheme}
                            className={iconBtnClass}
                            aria-label={isDark ? 'Modo claro' : 'Modo oscuro'}
                        >
                            {isDark ? <Sun className="h-4 w-4" /> : <Moon className="h-4 w-4" />}
                        </button>
                        {canLogin ? (
                            <Link
                                href={route('login')}
                                className={`inline-flex items-center gap-2 rounded-2xl px-5 py-2.5 text-sm font-semibold shadow-sm transition hover:-translate-y-0.5 ${
                                    lightOnHero
                                        ? 'border border-amber-400/50 bg-white/10 text-white backdrop-blur-sm hover:bg-white/20'
                                        : 'border border-amber-500/40 bg-[#0f2847] text-white hover:bg-[#1a3d5f] dark:border-amber-400/35 dark:bg-[#0f2847] dark:hover:bg-[#1e3a5f]'
                                }`}
                            >
                                <LogIn className={`h-4 w-4 ${lightOnHero ? 'text-amber-300' : 'text-amber-400'}`} />
                                Ingresar
                            </Link>
                        ) : null}
                    </div>

                    <div className="flex items-center gap-1 xl:hidden">
                        <button type="button" onClick={toggleTheme} className={iconBtnClass} aria-label="Tema">
                            {isDark ? <Sun className="h-5 w-5" /> : <Moon className="h-5 w-5" />}
                        </button>
                        <button
                            type="button"
                            className={iconBtnClass}
                            onClick={() => setMobileOpen(true)}
                            aria-expanded={mobileOpen}
                            aria-label="Abrir menú"
                        >
                            <Menu className="h-6 w-6" />
                        </button>
                    </div>
                </div>
            </header>

            <MobileNavMenu open={mobileOpen} onClose={() => setMobileOpen(false)} canLogin={canLogin} />
        </>
    );
}
