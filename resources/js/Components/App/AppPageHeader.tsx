import type { ReactNode } from 'react';

type AppPageHeaderProps = {
    title: string;
    description?: string;
    actions?: ReactNode;
    eyebrow?: string;
};

export function AppPageHeader({
    title,
    description,
    actions,
    eyebrow,
}: AppPageHeaderProps) {
    return (
        <div className="mb-6 flex flex-col gap-4 sm:mb-8 sm:flex-row sm:items-end sm:justify-between">
            <div>
                {eyebrow ? (
                    <p className="text-[11px] font-semibold uppercase tracking-[0.16em] text-brand-red">
                        {eyebrow}
                    </p>
                ) : null}
                <h1 className="text-2xl font-bold tracking-tight text-navy-900 sm:text-3xl dark:text-slate-100">
                    {title}
                </h1>
                {description ? (
                    <p className="mt-1 max-w-3xl text-sm text-plomo sm:text-base dark:text-slate-400">
                        {description}
                    </p>
                ) : null}
            </div>
            {actions ? <div className="shrink-0">{actions}</div> : null}
        </div>
    );
}
