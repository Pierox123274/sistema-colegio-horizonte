import { PublicThemeProvider } from '@/Components/Public/Premium/PublicThemeProvider';
import { Link } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';

/**
 * Layout ligero para registro, recuperar contraseña y verificación.
 * El login institucional premium usa LoginLayout.
 */
export default function GuestLayout({ children }: PropsWithChildren) {
    return (
        <PublicThemeProvider>
            <div className="flex min-h-screen flex-col items-center justify-center bg-institutional-surface px-4 py-10 transition-colors dark:bg-slate-950">
                <Link href={route('public.home')} className="mb-8 flex items-center gap-3">
                    <span className="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-amber-500 font-display text-lg font-bold text-[#071526] shadow-md">
                        H
                    </span>
                    <div className="text-left">
                        <p className="font-display text-lg font-bold text-slate-900 dark:text-white">
                            I.E.P. Horizonte
                        </p>
                        <p className="text-xs uppercase tracking-wider text-amber-600 dark:text-amber-400">
                            Portal institucional
                        </p>
                    </div>
                </Link>

                <div className="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200/70 bg-white/90 px-6 py-6 shadow-lg backdrop-blur-sm dark:border-white/10 dark:bg-slate-900/80 sm:px-8">
                    {children}
                </div>

                <Link
                    href={route('login')}
                    className="mt-6 text-sm font-medium text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                >
                    ← Volver al inicio de sesión
                </Link>
            </div>
        </PublicThemeProvider>
    );
}
