type SectionHeaderProps = {
    eyebrow?: string;
    title: string;
    description?: string;
    align?: 'left' | 'center';
};

export function SectionHeader({
    eyebrow,
    title,
    description,
    align = 'left',
}: SectionHeaderProps) {
    const alignClass = align === 'center' ? 'mx-auto max-w-3xl text-center' : 'max-w-2xl';
    return (
        <div className={alignClass}>
            {eyebrow ? (
                <p className="text-xs font-bold uppercase tracking-[0.2em] text-institutional-gold">
                    {eyebrow}
                </p>
            ) : null}
            <h2 className="mt-2 font-display text-2xl font-bold text-institutional-blue-900 dark:text-white sm:text-3xl">
                {title}
            </h2>
            {description ? (
                <p className="mt-3 text-plomo leading-relaxed dark:text-slate-400">{description}</p>
            ) : null}
        </div>
    );
}
