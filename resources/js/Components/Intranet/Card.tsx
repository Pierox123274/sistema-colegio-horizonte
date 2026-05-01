import { PropsWithChildren } from 'react';

type CardProps = PropsWithChildren<{
    className?: string;
    padding?: 'none' | 'sm' | 'md';
}>;

const paddingClass = {
    none: '',
    sm: 'p-4',
    md: 'p-5 sm:p-6',
};

export function Card({
    children,
    className = '',
    padding = 'md',
}: CardProps) {
    return (
        <div
            className={`rounded-xl border border-plomo/15 bg-white shadow-sm shadow-navy-900/5 ${paddingClass[padding]} ${className}`}
        >
            {children}
        </div>
    );
}
