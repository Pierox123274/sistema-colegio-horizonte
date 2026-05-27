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
            className={`rounded-2xl border border-slate-200/80 bg-white shadow-institutional transition-shadow duration-200 hover:shadow-institutional-lg dark:border-white/10 dark:bg-slate-900/85 ${paddingClass[padding]} ${className}`}
        >
            {children}
        </div>
    );
}
