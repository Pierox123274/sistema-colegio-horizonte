import type { ReactNode } from 'react';

type TableContainerProps = {
    title?: string;
    description?: string;
    /** Contenido de la tabla (thead/tbody o componente tabla) */
    children: ReactNode;
    /** Barra encima de la tabla (filtros, botón exportar, etc.) */
    toolbar?: ReactNode;
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
}: TableContainerProps) {
    return (
        <div className="overflow-hidden rounded-xl border border-plomo/15 bg-white shadow-sm shadow-navy-900/5">
            {(title || description || toolbar) && (
                <div className="border-b border-plomo/10 px-4 py-4 sm:px-6">
                    <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            {title && (
                                <h3 className="text-sm font-semibold text-navy-900">
                                    {title}
                                </h3>
                            )}
                            {description && (
                                <p className="mt-0.5 text-xs text-plomo">
                                    {description}
                                </p>
                            )}
                        </div>
                        {toolbar}
                    </div>
                </div>
            )}
            <div className="overflow-x-auto">{children}</div>
        </div>
    );
}
