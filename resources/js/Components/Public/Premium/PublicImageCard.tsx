import { Link } from '@inertiajs/react';
import { Reveal } from '@/Components/Public/Premium/Reveal';

type PublicImageCardProps = {
    image: string;
    title: string;
    subtitle?: string;
    href?: string;
    className?: string;
    aspect?: string;
};

export function PublicImageCard({
    image,
    title,
    subtitle,
    href,
    className = '',
    aspect = 'aspect-[4/3]',
}: PublicImageCardProps) {
    const inner = (
        <div
            className={`group relative overflow-hidden rounded-2xl border border-slate-200/70 shadow-md transition duration-500 hover:shadow-xl dark:border-white/10 ${aspect} ${className}`}
        >
            <img
                src={image}
                alt={title}
                className="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105"
                loading="lazy"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-[#071526]/90 via-[#071526]/25 to-transparent" />
            <div className="absolute inset-x-0 bottom-0 p-5 sm:p-6">
                {subtitle ? (
                    <p className="text-xs font-bold uppercase tracking-widest text-amber-400">
                        {subtitle}
                    </p>
                ) : null}
                <p className="mt-1 font-display text-lg font-bold text-white sm:text-xl">{title}</p>
            </div>
        </div>
    );

    return (
        <Reveal>
            {href ? (
                <Link href={href} className="block">
                    {inner}
                </Link>
            ) : (
                inner
            )}
        </Reveal>
    );
}
