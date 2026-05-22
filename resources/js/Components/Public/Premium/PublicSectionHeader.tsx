import { Reveal } from '@/Components/Public/Premium/Reveal';

type PublicSectionHeaderProps = {
    eyebrow?: string;
    title: string;
    description?: string;
    align?: 'left' | 'center';
    className?: string;
};

export function PublicSectionHeader({
    eyebrow,
    title,
    description,
    align = 'left',
    className = '',
}: PublicSectionHeaderProps) {
    const alignClass = align === 'center' ? 'mx-auto max-w-3xl text-center' : 'max-w-2xl';
    return (
        <Reveal className={alignClass + (className ? ` ${className}` : '')}>
            {eyebrow ? (
                <p className="text-xs font-bold uppercase tracking-[0.2em] text-amber-600 dark:text-amber-400">
                    {eyebrow}
                </p>
            ) : null}
            <h2 className="mt-2 font-display text-3xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                {title}
            </h2>
            {description ? (
                <p className="mt-4 text-base leading-relaxed text-slate-600 dark:text-slate-300">
                    {description}
                </p>
            ) : null}
        </Reveal>
    );
}
