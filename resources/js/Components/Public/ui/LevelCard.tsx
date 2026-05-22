import { Link } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import { ArrowRight } from 'lucide-react';

type LevelCardProps = {
    title: string;
    grades: string;
    description: string;
    href: string;
    icon: LucideIcon;
    accent?: string;
};

export function LevelCard({
    title,
    grades,
    description,
    href,
    icon: Icon,
    accent = 'border-slate-200/80 bg-white',
}: LevelCardProps) {
    return (
        <Link
            href={href}
            className={`group flex flex-col rounded-2xl border-2 p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-institutional-lg dark:bg-slate-900 ${accent}`}
        >
            <span className="flex h-12 w-12 items-center justify-center rounded-xl bg-institutional-blue-900/5 text-institutional-blue-900 dark:bg-white/5 dark:text-institutional-gold">
                <Icon className="h-6 w-6" />
            </span>
            <h3 className="mt-4 font-display text-xl font-bold text-institutional-blue-900 dark:text-white">
                {title}
            </h3>
            <p className="text-xs font-semibold text-institutional-gold">{grades}</p>
            <p className="mt-2 flex-1 text-sm text-plomo dark:text-slate-400">{description}</p>
            <span className="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-institutional-blue-900 group-hover:gap-2 dark:text-institutional-gold">
                Conocer más
                <ArrowRight className="h-4 w-4" />
            </span>
        </Link>
    );
}
