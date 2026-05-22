import { Link } from '@inertiajs/react';
import type { ComponentPropsWithoutRef, ReactNode } from 'react';

type Variant = 'primary' | 'secondary' | 'ghost' | 'portal';

const variants: Record<Variant, string> = {
    primary:
        'bg-institutional-gold text-institutional-blue-950 shadow-institutional hover:bg-institutional-gold-light hover:shadow-institutional-lg hover:-translate-y-0.5',
    secondary:
        'border-2 border-white/30 bg-white/10 text-white backdrop-blur-md hover:border-institutional-gold/60 hover:bg-white/15',
    ghost:
        'border border-institutional-blue-900/15 bg-white text-institutional-blue-900 shadow-sm hover:border-institutional-gold/50 hover:shadow-md dark:border-white/15 dark:bg-white/5 dark:text-white',
    portal:
        'border border-institutional-gold/40 bg-institutional-blue-900 text-white shadow-sm hover:bg-institutional-blue-800 hover:shadow-md',
};

type BaseProps = {
    variant?: Variant;
    children: ReactNode;
    className?: string;
};

type ButtonProps = BaseProps & ComponentPropsWithoutRef<'button'>;
type LinkProps = BaseProps & { href: string } & Omit<ComponentPropsWithoutRef<typeof Link>, 'href'>;

export function InstitutionalButton({
    variant = 'primary',
    children,
    className = '',
    ...props
}: ButtonProps) {
    return (
        <button
            type="button"
            className={`inline-flex items-center justify-center gap-2.5 rounded-2xl px-7 py-3.5 text-sm font-semibold tracking-wide transition-all duration-300 ${variants[variant]} ${className}`}
            {...props}
        >
            {children}
        </button>
    );
}

export function InstitutionalButtonLink({
    variant = 'primary',
    href,
    children,
    className = '',
    ...props
}: LinkProps) {
    return (
        <Link
            href={href}
            className={`inline-flex items-center justify-center gap-2.5 rounded-2xl px-7 py-3.5 text-sm font-semibold tracking-wide transition-all duration-300 ${variants[variant]} ${className}`}
            {...props}
        >
            {children}
        </Link>
    );
}
