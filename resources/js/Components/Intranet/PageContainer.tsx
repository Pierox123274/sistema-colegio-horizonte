import { PropsWithChildren } from 'react';

type PageContainerProps = PropsWithChildren<{
    className?: string;
    /** Ancho máximo del contenido */
    width?: 'default' | 'wide' | 'full';
}>;

const widthClass = {
    default: 'max-w-6xl',
    wide: 'max-w-7xl',
    full: 'max-w-full',
};

export function PageContainer({
    children,
    className = '',
    width = 'wide',
}: PageContainerProps) {
    return (
        <div
            className={`mx-auto w-full px-4 py-6 sm:px-6 lg:px-8 lg:py-8 ${widthClass[width]} ${className}`}
        >
            {children}
        </div>
    );
}
