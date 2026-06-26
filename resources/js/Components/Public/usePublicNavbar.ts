import { useEffect, useState } from 'react';

export function useNavbarScroll(): boolean {
    const [scrolled, setScrolled] = useState(false);

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 24);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    return scrolled;
}

export function useMobileBodyLock(mobileOpen: boolean): void {
    useEffect(() => {
        document.body.style.overflow = mobileOpen ? 'hidden' : '';
        return () => {
            document.body.style.overflow = '';
        };
    }, [mobileOpen]);
}

export function navSurfaceClass(scrolled: boolean, lightOnHero: boolean): string {
    if (scrolled) {
        return 'border-slate-200/80 bg-white/80 shadow-sm backdrop-blur-xl dark:border-white/10 dark:bg-[#071526]/80 dark:shadow-lg';
    }
    if (lightOnHero) {
        return 'border-transparent bg-gradient-to-b from-[#071526]/60 to-transparent';
    }
    return 'border-slate-200/70 bg-white/75 backdrop-blur-xl dark:border-white/10 dark:bg-[#071526]/70';
}

export function publicNavLinkClass(
    lightOnHero: boolean,
    routeActive: boolean,
    menuOpen = false,
): string {
    const base =
        'relative inline-flex items-center gap-1 rounded-xl px-3 py-2 text-sm font-medium transition-colors duration-150';
    const active = routeActive || menuOpen;

    if (lightOnHero) {
        return `${base} ${active ? 'bg-white/15 text-white' : 'text-white/90 hover:bg-white/10 hover:text-white'}`;
    }

    return `${base} ${
        active
            ? 'bg-slate-100 text-slate-900 dark:bg-white/10 dark:text-amber-400'
            : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-200 dark:hover:bg-white/10 dark:hover:text-white'
    }`;
}

export function publicNavIconBtnClass(lightOnHero: boolean): string {
    return lightOnHero
        ? 'rounded-xl p-2.5 text-white/90 transition hover:bg-white/10 hover:text-white'
        : 'rounded-xl p-2.5 text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-white/10 dark:hover:text-white';
}
