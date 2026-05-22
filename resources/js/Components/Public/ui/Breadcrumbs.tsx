import { Link } from '@inertiajs/react';
import { ChevronRight, Home } from 'lucide-react';

export type BreadcrumbItem = { label: string; href?: string };

type BreadcrumbsProps = {
    items: BreadcrumbItem[];
    light?: boolean;
};

export function Breadcrumbs({ items, light = false }: BreadcrumbsProps) {
    const text = light ? 'text-white/60' : 'text-slate-500 dark:text-slate-400';
    const active = light ? 'text-institutional-gold-light' : 'text-institutional-blue-900 dark:text-institutional-gold';
    const linkHover = light ? 'hover:text-white' : 'hover:text-institutional-blue-900 dark:hover:text-white';

    return (
        <nav aria-label="Breadcrumb" className="flex flex-wrap items-center gap-1 text-xs font-medium">
            <Link
                href={route('public.home')}
                className={`inline-flex items-center gap-1 ${text} ${linkHover}`}
            >
                <Home className="h-3.5 w-3.5" />
                Inicio
            </Link>
            {items.map((item, i) => {
                const isLast = i === items.length - 1;
                return (
                    <span key={`${item.label}-${i}`} className="inline-flex items-center gap-1">
                        <ChevronRight className={`h-3.5 w-3.5 ${text}`} />
                        {item.href && !isLast ? (
                            <Link href={item.href} className={`${text} ${linkHover}`}>
                                {item.label}
                            </Link>
                        ) : (
                            <span className={isLast ? active : text}>{item.label}</span>
                        )}
                    </span>
                );
            })}
        </nav>
    );
}
