import type { ReactNode } from 'react';

type SectionTitleProps = {
    title: string;
    description?: string;
    /** Acciones a la derecha del título (ej. botones) */
    actions?: ReactNode;
};

export function SectionTitle({
    title,
    description,
    actions,
}: SectionTitleProps) {
    return (
        <div className="mb-6 flex flex-col gap-4 sm:mb-8 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 className="text-2xl font-bold tracking-tight text-navy-900 sm:text-3xl">
                    {title}
                </h1>
                {description && (
                    <p className="mt-1 max-w-2xl text-sm text-plomo sm:text-base">
                        {description}
                    </p>
                )}
            </div>
            {actions ? <div className="shrink-0">{actions}</div> : null}
        </div>
    );
}
