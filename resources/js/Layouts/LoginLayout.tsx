import { LoginThemeToggle } from '@/Components/Auth/LoginThemeToggle';
import { PublicThemeProvider } from '@/Components/Public/Premium/PublicThemeProvider';
import { publicImage } from '@/Components/Public/data/publicImages';
import { Link } from '@inertiajs/react';
import {
    ArrowLeft,
    BookOpen,
    Brain,
    GraduationCap,
    MessageCircle,
    Shield,
} from 'lucide-react';
import type { PropsWithChildren } from 'react';

const features = [
    {
        icon: GraduationCap,
        title: 'Portal académico',
        text: 'Notas, matrículas y seguimiento en un solo lugar.',
    },
    {
        icon: BookOpen,
        title: 'Aula virtual',
        text: 'Recursos, tareas y evaluaciones digitales.',
    },
    {
        icon: Brain,
        title: 'Tutor inteligente',
        text: 'Acompañamiento adaptado al ritmo de aprendizaje.',
    },
    {
        icon: MessageCircle,
        title: 'Comunicación institucional',
        text: 'Avisos y convivencia con las familias.',
    },
];

function LoginLayoutInner({ children }: PropsWithChildren) {
    return (
        <div className="min-h-screen min-h-[100dvh] bg-[#f4f6f9] text-slate-900 transition-colors duration-300 dark:bg-[#060d18] dark:text-slate-100 lg:grid lg:grid-cols-2">
            {/* Panel izquierdo — cinematográfico */}
            <aside className="relative hidden overflow-hidden lg:block">
                <img
                    src={publicImage('hero')}
                    alt=""
                    className="absolute inset-0 h-full w-full scale-105 object-cover"
                />
                <div className="absolute inset-0 bg-[#071526]/88" />
                <div className="absolute inset-0 bg-gradient-to-br from-[#0a1f3d]/95 via-[#071526]/75 to-[#071526]/50" />
                <div className="absolute inset-0 bg-gradient-to-t from-[#071526] via-transparent to-[#071526]/20" />
                <div
                    className="absolute inset-0 opacity-[0.06] bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4z\'/%3E%3C/g%3E%3C/svg%3E')]"
                    aria-hidden
                />

                <div className="relative flex h-full min-h-screen flex-col justify-between px-10 py-10 xl:px-14 xl:py-12">
                    <div>
                        <Link
                            href={route('public.home')}
                            className="inline-flex items-center gap-2 text-sm font-medium text-white/65 transition hover:text-white"
                        >
                            <ArrowLeft className="h-4 w-4" aria-hidden />
                            Sitio institucional
                        </Link>

                        <div className="mt-12 flex items-center gap-4">
                            <span
                                className="flex h-[3.25rem] w-[3.25rem] items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 via-amber-400 to-amber-500 font-display text-xl font-bold text-[#071526] shadow-lg shadow-amber-500/20 ring-1 ring-white/20"
                                aria-hidden
                            >
                                H
                            </span>
                            <div>
                                <p className="font-display text-2xl font-bold tracking-tight text-white">
                                    I.E.P. Horizonte
                                </p>
                                <p className="mt-0.5 text-[11px] font-semibold uppercase tracking-[0.22em] text-amber-300/90">
                                    Portal institucional
                                </p>
                            </div>
                        </div>

                        <p className="mt-8 max-w-md text-base leading-relaxed text-slate-300/95">
                            Una experiencia digital segura para toda la comunidad educativa.
                        </p>
                    </div>

                    <ul className="grid gap-2.5 sm:grid-cols-2">
                        {features.map((f) => (
                            <li
                                key={f.title}
                                className="flex gap-3 rounded-xl border border-white/[0.08] bg-white/[0.04] px-3.5 py-3 backdrop-blur-[2px] transition hover:border-white/15 hover:bg-white/[0.07]"
                            >
                                <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-400/12 text-amber-400">
                                    <f.icon className="h-4 w-4" strokeWidth={2} />
                                </span>
                                <div className="min-w-0">
                                    <p className="text-sm font-semibold text-white">{f.title}</p>
                                    <p className="mt-0.5 text-xs leading-snug text-slate-400">{f.text}</p>
                                </div>
                            </li>
                        ))}
                    </ul>

                    <p className="text-[11px] text-white/35">
                        © {new Date().getFullYear()} I.E.P. Horizonte · Acceso autorizado
                    </p>
                </div>
            </aside>

            {/* Panel derecho — formulario */}
            <div className="relative flex min-h-screen min-h-[100dvh] flex-col">
                {/* Fondo suave derecho */}
                <div
                    className="pointer-events-none absolute inset-0 bg-gradient-to-br from-slate-50 via-[#f8f9fc] to-amber-50/40 dark:from-[#060d18] dark:via-[#0a1220] dark:to-[#0f1a2e]"
                    aria-hidden
                />
                <div
                    className="pointer-events-none absolute inset-0 opacity-[0.35] dark:opacity-20 bg-[radial-gradient(ellipse_80%_50%_at_50%_-10%,rgba(251,191,36,0.12),transparent)]"
                    aria-hidden
                />

                {/* Header fijo — toggle fuera del card */}
                <header className="relative z-20 flex shrink-0 items-center justify-between gap-4 border-b border-slate-200/60 bg-white/70 px-4 py-3 backdrop-blur-md dark:border-white/[0.06] dark:bg-[#060d18]/80 sm:px-6 lg:px-10">
                    <Link
                        href={route('public.home')}
                        className="inline-flex items-center gap-2 text-sm font-medium text-slate-600 transition hover:text-slate-900 lg:hidden dark:text-slate-400 dark:hover:text-white"
                    >
                        <ArrowLeft className="h-4 w-4" aria-hidden />
                        Inicio
                    </Link>
                    <div className="hidden items-center gap-2 lg:flex">
                        <Shield className="h-4 w-4 text-amber-600 dark:text-amber-400" aria-hidden />
                        <span className="text-sm font-medium text-slate-600 dark:text-slate-400">
                            Acceso seguro al portal
                        </span>
                    </div>
                    <div className="ml-auto flex items-center gap-1">
                        <LoginThemeToggle />
                    </div>
                </header>

                <main className="relative z-10 flex flex-1 flex-col justify-center overflow-x-hidden px-4 py-8 sm:px-6 lg:px-10 lg:py-12">
                    <div className="mx-auto w-full max-w-[26rem]">
                        {/* Branding móvil — fuera del card */}
                        <div className="mb-6 text-center lg:hidden">
                            <div className="inline-flex items-center gap-3">
                                <span
                                    className="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-amber-500 font-display text-lg font-bold text-[#071526] shadow-md"
                                    aria-hidden
                                >
                                    H
                                </span>
                                <div className="text-left">
                                    <p className="font-display text-lg font-bold text-slate-900 dark:text-white">
                                        I.E.P. Horizonte
                                    </p>
                                    <p className="text-[10px] font-semibold uppercase tracking-[0.18em] text-amber-700 dark:text-amber-400">
                                        Portal institucional seguro
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Resumen móvil del panel izquierdo */}
                        <p className="mb-6 text-center text-xs leading-relaxed text-slate-500 lg:hidden dark:text-slate-400">
                            Experiencia digital para familias, docentes y equipo del colegio.
                        </p>

                        <div className="rounded-3xl border border-slate-200/80 bg-white/90 p-8 shadow-[0_8px_40px_-12px_rgba(15,40,71,0.18)] backdrop-blur-sm dark:border-white/[0.08] dark:bg-slate-900/75 dark:shadow-[0_8px_40px_-12px_rgba(0,0,0,0.55)] sm:p-9">
                            {children}
                        </div>

                        <p className="mt-6 text-center text-xs text-slate-500 dark:text-slate-500">
                            ¿Necesitas ayuda?{' '}
                            <Link
                                href={route('public.contacto')}
                                className="font-semibold text-amber-700 hover:underline dark:text-amber-400"
                            >
                                Contacto institucional
                            </Link>
                        </p>
                    </div>
                </main>
            </div>
        </div>
    );
}

export default function LoginLayout({ children }: PropsWithChildren) {
    return (
        <PublicThemeProvider>
            <LoginLayoutInner>{children}</LoginLayoutInner>
        </PublicThemeProvider>
    );
}
