import { Breadcrumbs, type BreadcrumbItem } from './Breadcrumbs';

type PageHeroProps = {
    title: string;
    subtitle?: string;
    breadcrumbs: BreadcrumbItem[];
    compact?: boolean;
};

export function PageHero({ title, subtitle, breadcrumbs, compact = false }: PageHeroProps) {
    return (
        <div className="relative overflow-hidden border-b border-slate-200/60 bg-gradient-to-br from-institutional-blue-950 via-institutional-blue-900 to-institutional-blue-800 text-white dark:border-white/10">
            <div
                className="absolute inset-0 bg-[radial-gradient(ellipse_70%_50%_at_50%_-20%,rgba(201,162,39,0.15),transparent)]"
                aria-hidden
            />
            <div
                className={`relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 ${compact ? 'py-12 sm:py-14' : 'py-14 sm:py-20'}`}
            >
                <Breadcrumbs items={breadcrumbs} light />
                <h1 className="mt-4 font-display text-3xl font-extrabold tracking-tight sm:text-4xl lg:text-5xl">
                    {title}
                </h1>
                {subtitle ? (
                    <p className="mt-4 max-w-2xl text-base leading-relaxed text-white/78 sm:text-lg">
                        {subtitle}
                    </p>
                ) : null}
            </div>
        </div>
    );
}
