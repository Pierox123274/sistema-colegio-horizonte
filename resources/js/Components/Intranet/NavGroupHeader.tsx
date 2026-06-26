import { intranetNavIcon } from '@/Components/Intranet/navIcons';
import type { SidebarNavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { ChevronDown } from 'lucide-react';

type NavGroupHeaderProps = {
    item: SidebarNavItem;
    showLabels: boolean;
    headerBase: string;
    headerTone: string;
    iconSlot: React.ReactNode;
    expanded: boolean;
    subPanelId: string;
    parentHref: string | null;
    onNavigate?: () => void;
    onToggle: () => void;
};

export function NavGroupHeader({
    item,
    showLabels,
    headerBase,
    headerTone,
    iconSlot,
    expanded,
    subPanelId,
    parentHref,
    onNavigate,
    onToggle,
}: NavGroupHeaderProps) {
    const chevron = (
        <ChevronDown
            className={`h-4 w-4 shrink-0 text-white/60 transition-transform duration-300 ease-out ${
                expanded ? 'rotate-180' : 'rotate-0'
            }`}
            strokeWidth={2}
            aria-hidden
        />
    );

    if (parentHref) {
        return (
            <div className={`${headerBase} ${headerTone} ${showLabels ? '' : 'justify-center'}`}>
                <Link
                    href={parentHref}
                    onClick={onNavigate}
                    title={item.label}
                    className="flex min-w-0 flex-1 items-center gap-3"
                >
                    {iconSlot}
                    {showLabels ? (
                        <span className="min-w-0 flex-1 truncate text-left text-xs font-semibold uppercase tracking-wider text-white/80">
                            {item.label}
                        </span>
                    ) : (
                        <span className="sr-only">{item.label}</span>
                    )}
                </Link>
                <button
                    type="button"
                    aria-expanded={expanded}
                    aria-controls={subPanelId}
                    aria-label={expanded ? 'Ocultar submenú' : 'Mostrar submenú'}
                    onClick={(e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        onToggle();
                    }}
                    className="flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-white/60 transition hover:bg-white/10 hover:text-white"
                >
                    {chevron}
                </button>
            </div>
        );
    }

    return (
        <button
            type="button"
            aria-expanded={expanded}
            aria-controls={subPanelId}
            title={item.label}
            onClick={onToggle}
            className={`${headerBase} ${headerTone} ${
                showLabels ? '' : 'justify-center lg:justify-center'
            }`}
        >
            {iconSlot}
            {showLabels ? (
                <>
                    <span className="min-w-0 flex-1 truncate text-left text-xs font-semibold uppercase tracking-wider text-white/80">
                        {item.label}
                    </span>
                    {chevron}
                </>
            ) : (
                <>
                    <span className="sr-only">{item.label}</span>
                    {chevron}
                </>
            )}
        </button>
    );
}

export function navGroupIconSlot(item: SidebarNavItem) {
    const Icon = intranetNavIcon(item.icon);
    return (
        <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/5">
            <Icon className="h-5 w-5" strokeWidth={1.75} />
        </span>
    );
}
