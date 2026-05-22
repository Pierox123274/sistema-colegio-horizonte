import { Link } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import { Reveal } from '@/Components/Public/Premium/Reveal';

type PublicEditorialCardProps = {
    image: string;
    title: string;
    description: string;
    href?: string;
    size?: 'default' | 'large' | 'tall';
    delay?: number;
};

export function PublicEditorialCard({
    image,
    title,
    description,
    href,
    size = 'default',
    delay = 0,
}: PublicEditorialCardProps) {
    const sizeClass =
        size === 'large'
            ? 'min-h-[22rem] sm:min-h-[26rem] md:col-span-2'
            : size === 'tall'
              ? 'min-h-[20rem]'
              : 'min-h-[16rem]';

    const content = (
        <div
            className={`group relative overflow-hidden rounded-2xl border border-slate-200/60 shadow-lg transition duration-500 hover:shadow-2xl dark:border-white/10 ${sizeClass}`}
        >
            <img
                src={image}
                alt={title}
                className="absolute inset-0 h-full w-full object-cover transition duration-700 ease-out group-hover:scale-[1.04]"
                loading="lazy"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-[#071526]/95 via-[#071526]/35 to-[#071526]/10 transition duration-500 group-hover:via-[#071526]/45" />
            <div className="absolute inset-0 flex flex-col justify-end p-6 sm:p-8">
                <h3 className="font-display text-xl font-bold text-white sm:text-2xl">{title}</h3>
                <p className="mt-2 max-w-md text-sm leading-relaxed text-slate-200 sm:text-base">
                    {description}
                </p>
                {href ? (
                    <span className="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-amber-400 transition group-hover:gap-3">
                        Conocer más
                        <ArrowRight className="h-4 w-4" />
                    </span>
                ) : null}
            </div>
        </div>
    );

    return (
        <Reveal delay={delay}>
            {href ? <Link href={href}>{content}</Link> : content}
        </Reveal>
    );
}
