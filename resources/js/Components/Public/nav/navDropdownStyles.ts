/** Estilos del dropdown según tema (evita depender solo de dark: en el DOM). */

export type NavDropdownStyles = {
    panel: string;
    panelInner: string;
    itemLink: string;
    itemTitle: string;
    itemDesc: string;
    iconWrap: string;
    iconWrapSm: string;
    chevron: string;
    divider: string;
    sectionLabel: string;
    nivelCard: string;
    nivelCardTitle: string;
    nivelCardGrades: string;
    nivelCardCta: string;
    timelineItem: string;
    timelineTitle: string;
    timelineBadge: string;
    galleryTile: string[];
};

const light: NavDropdownStyles = {
    panel:
        'overflow-hidden rounded-2xl border border-slate-200 bg-white/95 shadow-xl backdrop-blur-xl',
    panelInner: 'p-3',
    itemLink:
        'group flex items-start gap-3 rounded-xl px-4 py-3 transition-all duration-200 hover:bg-slate-50 hover:translate-x-0.5',
    itemTitle: 'text-sm font-medium text-slate-900 transition-colors group-hover:text-slate-900',
    itemDesc: 'mt-1 block text-xs text-slate-600 transition-colors group-hover:text-slate-700',
    iconWrap:
        'flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-amber-600 transition duration-200 group-hover:bg-amber-50',
    iconWrapSm:
        'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-amber-600 transition duration-200 group-hover:bg-amber-50',
    chevron:
        'h-4 w-4 shrink-0 text-slate-400 opacity-0 transition-all duration-200 group-hover:opacity-100 group-hover:text-slate-600',
    divider: 'border-slate-200',
    sectionLabel: 'text-xs font-bold uppercase tracking-widest text-amber-600',
    nivelCard:
        'group flex h-full flex-col rounded-xl border border-slate-200 bg-slate-50 p-5 transition-all duration-200 hover:-translate-y-0.5 hover:border-amber-400/40 hover:bg-white hover:shadow-lg',
    nivelCardTitle: 'mt-4 font-display text-lg font-bold text-slate-900',
    nivelCardGrades: 'text-xs font-medium text-amber-600',
    nivelCardCta:
        'mt-4 inline-flex items-center gap-1 text-xs font-semibold text-slate-800 transition duration-200 group-hover:gap-2',
    timelineItem:
        'relative flex gap-4 rounded-lg px-2 py-2 transition-all duration-200 hover:bg-slate-50',
    timelineTitle: 'text-sm font-medium text-slate-900',
    timelineBadge:
        'relative z-10 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-800 text-xs font-bold text-amber-500',
    galleryTile: ['bg-slate-200', 'bg-amber-200/80', 'bg-sky-200/80', 'bg-slate-300'],
};

const dark: NavDropdownStyles = {
    panel:
        'overflow-hidden rounded-2xl border border-white/10 bg-[#071526]/98 shadow-[0_20px_80px_rgba(0,0,0,0.55)] ring-1 ring-white/5 backdrop-blur-xl',
    panelInner: 'p-3',
    itemLink:
        'group flex items-start gap-3 rounded-xl px-4 py-3 transition-all duration-200 hover:bg-white/10 hover:translate-x-0.5',
    itemTitle:
        'text-sm font-medium text-white transition-colors group-hover:text-yellow-300',
    itemDesc: 'mt-1 block text-xs text-slate-300 transition-colors group-hover:text-slate-200',
    iconWrap:
        'flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white/10 text-yellow-400 transition duration-200 group-hover:bg-white/15 group-hover:text-yellow-300',
    iconWrapSm:
        'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10 text-yellow-400 transition duration-200 group-hover:bg-white/15 group-hover:text-yellow-300',
    chevron:
        'h-4 w-4 shrink-0 text-slate-400 opacity-0 transition-all duration-200 group-hover:opacity-100 group-hover:text-yellow-300',
    divider: 'border-white/10',
    sectionLabel: 'text-xs font-bold uppercase tracking-widest text-yellow-400',
    nivelCard:
        'group flex h-full flex-col rounded-xl border border-white/10 bg-[#0a1d33]/90 p-5 transition-all duration-200 hover:-translate-y-0.5 hover:border-yellow-400/30 hover:bg-white/10 hover:shadow-[0_8px_32px_rgba(0,0,0,0.4)]',
    nivelCardTitle: 'mt-4 font-display text-lg font-bold text-white group-hover:text-yellow-300',
    nivelCardGrades: 'text-xs font-medium text-yellow-400',
    nivelCardCta:
        'mt-4 inline-flex items-center gap-1 text-xs font-semibold text-yellow-400 transition duration-200 group-hover:gap-2 group-hover:text-yellow-300',
    timelineItem:
        'relative flex gap-4 rounded-lg px-2 py-2 transition-all duration-200 hover:bg-white/10',
    timelineTitle: 'text-sm font-medium text-white',
    timelineBadge:
        'relative z-10 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#0f2847] text-xs font-bold text-yellow-400 ring-1 ring-white/10',
    galleryTile: [
        'bg-slate-800',
        'bg-amber-500/35',
        'bg-sky-700/45',
        'bg-slate-700',
    ],
};

export function getNavDropdownStyles(isDark: boolean): NavDropdownStyles {
    return isDark ? dark : light;
}

/** @deprecated Usar getNavDropdownStyles(isDark).panel */
export const dropdownPanelClass = light.panel;
