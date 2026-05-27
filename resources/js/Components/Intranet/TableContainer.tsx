import type { ReactNode } from 'react';

type TableContainerProps = {
    title?: string;
    description?: string;
    /** Contenido de la tabla (thead/tbody o componente tabla) */
    children: ReactNode;
    /** Barra encima de la tabla (filtros, botón exportar, etc.) */
    toolbar?: ReactNode;
    stickyHeader?: boolean;
};

/**
 * Contenedor visual para tablas de datos (módulos futuros).
 * No incluye lógica de datos.
 */
export function TableContainer({
    title,
    description,
    children,
    toolbar,
    stickyHeader = false,
}: TableContainerProps) {
    return (
        <div className="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-institutional dark:border-white/10 dark:bg-slate-900/85">
            {(title || description || toolbar) && (
                <div className="border-b border-slate-200/80 px-4 py-4 sm:px-6 dark:border-white/10">
                    <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            {title && (
                                <h3 className="text-sm font-semibold text-navy-900 dark:text-slate-100">
                                    {title}
                                </h3>
                            )}
                            {description && (
                                <p className="mt-0.5 text-xs text-plomo dark:text-slate-400">
                                    {description}
                                </p>
                            )}
                        </div>
                        {toolbar}
                    </div>
                </div>
            )}
            <div
                className={`overflow-x-auto ${
                    stickyHeader
                        ? '[&_thead]:sticky [&_thead]:top-0 [&_thead]:z-10'
                        : ''
                }`}
            >
                {children}
            </div>
        </div>
    );
}
