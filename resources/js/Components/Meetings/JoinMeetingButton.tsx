import { Link } from '@inertiajs/react';
import { Video } from 'lucide-react';

type Props = {
    href: string;
    disabled?: boolean;
    label?: string;
    className?: string;
};

export default function JoinMeetingButton({
    href,
    disabled = false,
    label = 'Unirse',
    className = '',
}: Props) {
    if (disabled) {
        return (
            <button
                type="button"
                disabled
                className={`inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-plomo/20 bg-slate-100 px-4 py-2.5 text-sm font-semibold text-plomo opacity-60 dark:border-white/10 dark:bg-slate-800 ${className}`}
            >
                <Video className="h-4 w-4" />
                {label}
            </button>
        );
    }

    return (
        <Link
            href={href}
            className={`inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-navy-900 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-navy-950 dark:bg-brand-yellow dark:text-navy-950 dark:hover:bg-brand-yellow/90 ${className}`}
        >
            <Video className="h-4 w-4" />
            {label}
        </Link>
    );
}
