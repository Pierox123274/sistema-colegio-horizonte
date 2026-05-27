import type { PropsWithChildren, ReactNode } from 'react';

export function AppSection({
    title,
    description,
    children,
    className = '',
}: PropsWithChildren<{
    title?: ReactNode;
    description?: ReactNode;
    className?: string;
}>) {
    return (
        <section className={`space-y-4 ${className}`}>
            {title ? (
                <header>
                    <h2 className="text-lg font-semibold text-navy-900 dark:text-slate-100">{title}</h2>
                    {description ? (
                        <p className="mt-1 text-sm text-plomo dark:text-slate-400">{description}</p>
                    ) : null}
                </header>
            ) : null}
            {children}
        </section>
    );
}
