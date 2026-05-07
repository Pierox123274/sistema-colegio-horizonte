import { intranetNavIcon } from '@/Components/Intranet/navIcons';
import type { SidebarNavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import {
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    PanelLeftClose,
} from 'lucide-react';
import { useEffect, useId, useRef, useState } from 'react';

type SidebarProps = {
    items: SidebarNavItem[];
    collapsed: boolean;
    onToggleCollapse: () => void;
    mobileOpen: boolean;
    onMobileClose: () => void;
    primaryRole: string;
};

const EMPTY_NAV_CHILDREN: SidebarNavItem[] = [];

function NavRow({
    item,
    showLabels,
    currentPath,
    onNavigate,
}: {
    item: SidebarNavItem;
    showLabels: boolean;
    currentPath: string;
    onNavigate?: () => void;
}) {
    const Icon = intranetNavIcon(item.icon);
    let isActive = false;
    if (!item.disabled && item.href) {
        const itemPath = item.href.startsWith('http')
            ? new URL(item.href).pathname
            : item.href.split('?')[0];
        isActive =
            currentPath === itemPath ||
            currentPath.startsWith(itemPath + '/');
    }

    const base =
        'group flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors';
    const active =
        'bg-white/10 text-white ring-1 ring-white/15';
    const idle = 'text-white/75 hover:bg-white/5 hover:text-white';
    const disabled =
        'cursor-not-allowed text-white/35 hover:bg-transparent hover:text-white/35';

    const content = (
        <>
            <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/5">
                <Icon className="h-5 w-5" strokeWidth={1.75} />
            </span>
            <span
                className={`min-w-0 flex-1 truncate text-left ${showLabels ? '' : 'sr-only'}`}
            >
                {item.label}
            </span>
            {item.disabled && showLabels && (
                <span className="shrink-0 rounded bg-white/10 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white/60">
                    Pronto
                </span>
            )}
        </>
    );

    if (item.children && item.children.length > 0) {
        return null;
    }

    if (item.disabled || !item.href) {
        return (
            <button
                type="button"
                disabled
                title={item.label}
                className={`${base} ${disabled}`}
            >
                {content}
            </button>
        );
    }

    return (
        <Link
            href={item.href}
            onClick={onNavigate}
            title={!showLabels ? item.label : undefined}
            className={`${base} ${isActive ? active : idle}`}
        >
            {content}
        </Link>
    );
}

function childPathFromHref(href: string): string {
    return href.startsWith('http')
        ? new URL(href).pathname
        : href.split('?')[0];
}

/** True si la ruta actual coincide con el ítem o es subruta (misma lógica que NavRow). */
function pathMatchesItem(currentPath: string, href: string | null): boolean {
    if (!href) {
        return false;
    }
    const itemPath = childPathFromHref(href);
    return (
        currentPath === itemPath || currentPath.startsWith(itemPath + '/')
    );
}

function isPathInsideNavGroup(
    currentPath: string,
    children: SidebarNavItem[],
): boolean {
    return children.some(
        (c) => !c.disabled && c.href && pathMatchesItem(currentPath, c.href),
    );
}

function NavGroup({
    item,
    showLabels,
    currentPath,
    onNavigate,
}: {
    item: SidebarNavItem;
    showLabels: boolean;
    currentPath: string;
    onNavigate?: () => void;
}) {
    const Icon = intranetNavIcon(item.icon);
    const children = item.children ?? EMPTY_NAV_CHILDREN;
    const subPanelId = useId();

    const insideGroup = isPathInsideNavGroup(currentPath, children);

    const [expanded, setExpanded] = useState(() =>
        isPathInsideNavGroup(currentPath, children),
    );

    const prevInsideRef = useRef(insideGroup);

    useEffect(() => {
        const kids = item.children ?? EMPTY_NAV_CHILDREN;
        const inside = isPathInsideNavGroup(currentPath, kids);
        if (inside && !prevInsideRef.current) {
            setExpanded(true);
        }
        if (!inside && prevInsideRef.current) {
            setExpanded(false);
        }
        prevInsideRef.current = inside;
        // Solo al cambiar la URL: abrir al entrar al grupo y cerrar al salir (no pisar el toggle dentro del grupo).
    }, [currentPath, item.children]);

    const headerActive = insideGroup;

    const headerBase =
        'group/nav-head flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors';
    const headerTone = headerActive
        ? 'bg-white/10 text-white ring-1 ring-white/15'
        : 'text-white/75 hover:bg-white/5 hover:text-white';

    return (
        <div className="space-y-1">
            <button
                type="button"
                aria-expanded={expanded}
                aria-controls={subPanelId}
                title={item.label}
                onClick={() => setExpanded((open) => !open)}
                className={`${headerBase} ${headerTone} ${
                    showLabels ? '' : 'justify-center lg:justify-center'
                }`}
            >
                <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/5">
                    <Icon className="h-5 w-5" strokeWidth={1.75} />
                </span>
                {showLabels ? (
                    <>
                        <span className="min-w-0 flex-1 truncate text-left text-xs font-semibold uppercase tracking-wider text-white/80">
                            {item.label}
                        </span>
                        <ChevronDown
                            className={`h-4 w-4 shrink-0 text-white/60 transition-transform duration-300 ease-out ${
                                expanded ? 'rotate-180' : 'rotate-0'
                            }`}
                            strokeWidth={2}
                            aria-hidden
                        />
                    </>
                ) : (
                    <>
                        <span className="sr-only">{item.label}</span>
                        <ChevronDown
                            className={`h-4 w-4 shrink-0 text-white/60 transition-transform duration-300 ease-out ${
                                expanded ? 'rotate-180' : 'rotate-0'
                            }`}
                            strokeWidth={2}
                            aria-hidden
                        />
                    </>
                )}
            </button>
            <div
                id={subPanelId}
                className={`grid overflow-hidden transition-[grid-template-rows] duration-300 ease-in-out ${
                    expanded ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'
                }`}
            >
                <div className="min-h-0">
                    <div className="ml-2 space-y-1 border-l border-white/10 pl-3 pt-0.5">
                        {children.map((child) => (
                            <NavRow
                                key={child.label + (child.href ?? '')}
                                item={child}
                                showLabels={showLabels}
                                currentPath={currentPath}
                                onNavigate={onNavigate}
                            />
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}

export function Sidebar({
    items,
    collapsed,
    onToggleCollapse,
    mobileOpen,
    onMobileClose,
    primaryRole,
}: SidebarProps) {
    const { url } = usePage();
    const currentPath = url.split('?')[0];
    const showLabels = !collapsed || mobileOpen;

    const asideWidth = collapsed && !mobileOpen ? 'lg:w-[4.5rem]' : 'lg:w-64';
    const translate =
        mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0';

    return (
        <aside
            className={`fixed inset-y-0 left-0 z-50 flex w-[min(18rem,88vw)] flex-col border-r border-white/10 bg-navy-950 shadow-2xl shadow-navy-950/50 transition-[width,transform] duration-200 ease-out ${asideWidth} ${translate}`}
        >
            <div className="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-white/10 px-3">
                <Link
                    href={route('dashboard')}
                    onClick={() => onMobileClose()}
                    className={`flex min-w-0 flex-1 items-center gap-3 ${collapsed && !mobileOpen ? 'lg:justify-center' : ''}`}
                >
                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-yellow font-serif text-lg font-bold text-navy-950 shadow-sm">
                        H
                    </div>
                    {showLabels && (
                        <div className="min-w-0 leading-tight lg:block">
                            <p className="truncate text-sm font-bold tracking-tight text-white">
                                I.E.P. Horizonte
                            </p>
                            <p className="truncate text-[11px] font-medium uppercase tracking-widest text-brand-yellow">
                                Intranet
                            </p>
                        </div>
                    )}
                </Link>
                <button
                    type="button"
                    onClick={onToggleCollapse}
                    title={collapsed ? 'Expandir menú' : 'Colapsar menú'}
                    className="hidden h-9 w-9 shrink-0 items-center justify-center rounded-lg text-white/70 transition-colors hover:bg-white/10 hover:text-white lg:flex"
                >
                    {collapsed ? (
                        <ChevronRight className="h-5 w-5" />
                    ) : (
                        <ChevronLeft className="h-5 w-5" />
                    )}
                </button>
                <button
                    type="button"
                    onClick={onMobileClose}
                    className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-white/80 hover:bg-white/10 lg:hidden"
                    aria-label="Cerrar menú"
                >
                    <PanelLeftClose className="h-5 w-5" />
                </button>
            </div>

            <nav className="flex-1 space-y-1 overflow-y-auto p-3">
                {items.map((item) =>
                    item.children && item.children.length > 0 ? (
                        <NavGroup
                            key={item.label}
                            item={item}
                            showLabels={showLabels}
                            currentPath={currentPath}
                            onNavigate={onMobileClose}
                        />
                    ) : (
                        <NavRow
                            key={item.label + (item.href ?? '')}
                            item={item}
                            showLabels={showLabels}
                            currentPath={currentPath}
                            onNavigate={onMobileClose}
                        />
                    ),
                )}
            </nav>

            <div className="shrink-0 border-t border-white/10 p-3">
                <div className="rounded-lg bg-black/20 px-3 py-2 text-xs text-white/60">
                    {showLabels ? (
                        <>
                            <p className="font-semibold uppercase tracking-wide text-white/40">
                                Rol
                            </p>
                            <p className="mt-0.5 truncate text-sm text-white/90">
                                {primaryRole}
                            </p>
                        </>
                    ) : (
                        <p
                            className="truncate text-center text-[10px] font-medium text-white/70"
                            title={primaryRole}
                        >
                            {primaryRole.slice(0, 4)}
                        </p>
                    )}
                </div>
            </div>
        </aside>
    );
}
