import { Link } from '@inertiajs/react';

export type BreadcrumbItem = {
    label: string;
    href?: string;
};

export function IntranetBreadcrumbs({ items }: { items: BreadcrumbItem[] }) {
    return (
        <nav
            className="mb-6 flex flex-wrap items-center gap-2 text-sm text-plomo"
            aria-label="Migas de pan"
        >
            <Link
                href={route('dashboard')}
                className="font-medium text-navy-900 underline-offset-2 hover:underline"
            >
                Panel
            </Link>
            {items.map((crumb, i) => {
                const isLast = i === items.length - 1;
                return (
                    <span key={`${crumb.label}-${i}`} className="flex items-center gap-2">
                        <span className="text-plomo/50" aria-hidden>
                            /
                        </span>
                        {!isLast && crumb.href ? (
                            <Link
                                href={crumb.href}
                                className="font-medium text-navy-900 underline-offset-2 hover:underline"
                            >
                                {crumb.label}
                            </Link>
                        ) : (
                            <span
                                className={`font-semibold ${isLast ? 'text-navy-900' : 'text-plomo'}`}
                            >
                                {crumb.label}
                            </span>
                        )}
                    </span>
                );
            })}
        </nav>
    );
}
