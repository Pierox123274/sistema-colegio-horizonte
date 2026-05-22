import { Link } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import { SectionHeader } from '@/Components/Public/ui/SectionHeader';

type HomeSectionTeaserProps = {
    eyebrow: string;
    title: string;
    description: string;
    href: string;
    linkLabel: string;
    children?: React.ReactNode;
    altBackground?: boolean;
};

export function HomeSectionTeaser({
    eyebrow,
    title,
    description,
    href,
    linkLabel,
    children,
    altBackground = false,
}: HomeSectionTeaserProps) {
    return (
        <section
            className={`py-16 sm:py-20 ${altBackground ? 'section-institutional-alt section-separator' : 'section-institutional'}`}
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                    <SectionHeader eyebrow={eyebrow} title={title} description={description} />
                    <Link
                        href={href}
                        className="inline-flex shrink-0 items-center gap-2 rounded-2xl border border-institutional-blue-900/15 bg-white px-6 py-3 text-sm font-semibold text-institutional-blue-900 shadow-sm transition hover:border-institutional-gold/40 hover:shadow-md dark:border-white/15 dark:bg-white/5 dark:text-white"
                    >
                        {linkLabel}
                        <ArrowRight className="h-4 w-4" />
                    </Link>
                </div>
                {children ? <div className="mt-10">{children}</div> : null}
            </div>
        </section>
    );
}
