import type { PropsWithChildren, ReactNode } from 'react';

type AppToolbarProps = PropsWithChildren<{
    leading?: ReactNode;
    trailing?: ReactNode;
    className?: string;
}>;

export function AppToolbar({
    leading,
    trailing,
    className = '',
    children,
}: AppToolbarProps) {
    return (
        <div className={`flex flex-wrap items-center justify-between gap-3 ${className}`}>
            <div className="flex flex-wrap items-center gap-3">
                {leading}
                {children}
            </div>
            <div className="flex flex-wrap items-center gap-2">{trailing}</div>
        </div>
    );
}
