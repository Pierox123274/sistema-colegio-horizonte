import { ButtonHTMLAttributes } from 'react';

export default function PrimaryButton({
    className = '',
    disabled,
    children,
    ...props
}: ButtonHTMLAttributes<HTMLButtonElement>) {
    return (
        <button
            {...props}
            className={`inline-flex items-center rounded-lg border border-transparent bg-navy-900 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-navy-800 focus:bg-navy-800 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:ring-offset-2 active:bg-navy-950 disabled:opacity-50 dark:ring-offset-slate-950 ${className}`}
            disabled={disabled}
        >
            {children}
        </button>
    );
}
