import Checkbox from '@/Components/Checkbox';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import LoginLayout from '@/Layouts/LoginLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import {
    AlertCircle,
    ArrowRight,
    Eye,
    EyeOff,
    Loader2,
    Lock,
    LogIn,
    Mail,
    ShieldCheck,
} from 'lucide-react';
import { FormEventHandler, useState } from 'react';

type DemoAccount = {
    label: string;
    email: string;
    password: string;
};

const demoAccounts: DemoAccount[] = [
    { label: 'Admin', email: 'test@example.com', password: 'password' },
    { label: 'Docente', email: 'docente@demo.com', password: 'password' },
    { label: 'Estudiante', email: 'estudiante@demo.com', password: 'password' },
];

const inputClass =
    'block h-11 w-full rounded-xl border-slate-200/90 bg-white py-0 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-white/12 dark:bg-slate-950/50 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-amber-400 dark:focus:ring-amber-400/25';

export default function Login({
    status,
    canResetPassword,
    showDemoLogins = false,
}: {
    status?: string;
    canResetPassword: boolean;
    showDemoLogins?: boolean;
}) {
    const [showPassword, setShowPassword] = useState(false);
    const isDemoVisible = import.meta.env.DEV && showDemoLogins;

    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false as boolean,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    const fillDemo = (account: DemoAccount) => {
        setData({
            email: account.email,
            password: account.password,
            remember: false,
        });
    };

    const authError = errors.email ?? errors.password;

    return (
        <LoginLayout>
            <Head title="Iniciar sesión — I.E.P. Horizonte" />

            <header className="mb-8">
                <p className="inline-flex items-center gap-1.5 rounded-full border border-amber-500/25 bg-amber-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-amber-800 dark:text-amber-300">
                    <ShieldCheck className="h-3.5 w-3.5" aria-hidden />
                    Portal institucional seguro
                </p>
                <h1 className="mt-4 font-display text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-[1.65rem]">
                    Iniciar sesión
                </h1>
                <p className="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                    Ingresa con tu cuenta del colegio. Familias, docentes y personal autorizado.
                </p>
            </header>

            {status ? (
                <div
                    role="status"
                    className="mb-6 rounded-xl border border-emerald-200/90 bg-emerald-50/90 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/25 dark:bg-emerald-950/50 dark:text-emerald-200"
                >
                    {status}
                </div>
            ) : null}

            {authError ? (
                <div
                    role="alert"
                    className="mb-6 flex gap-3 rounded-xl border border-red-200/90 bg-red-50/90 px-4 py-3 text-sm text-red-800 dark:border-red-500/25 dark:bg-red-950/50 dark:text-red-200"
                >
                    <AlertCircle className="mt-0.5 h-5 w-5 shrink-0" aria-hidden />
                    <p>{authError}</p>
                </div>
            ) : null}

            <form onSubmit={submit} className="space-y-5" noValidate>
                <div>
                    <InputLabel
                        htmlFor="email"
                        value="Correo electrónico"
                        className="text-sm font-medium text-slate-700 dark:text-slate-300"
                    />
                    <div className="relative mt-2">
                        <Mail
                            className="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                            aria-hidden
                        />
                        <TextInput
                            id="email"
                            type="email"
                            name="email"
                            value={data.email}
                            placeholder="nombre@ejemplo.com"
                            className={`${inputClass} pl-10`}
                            autoComplete="username"
                            isFocused={true}
                            onChange={(e) => setData('email', e.target.value)}
                            aria-invalid={Boolean(errors.email && !authError)}
                        />
                    </div>
                    {!authError ? <InputError message={errors.email} className="mt-1.5" /> : null}
                </div>

                <div>
                    <InputLabel
                        htmlFor="password"
                        value="Contraseña"
                        className="text-sm font-medium text-slate-700 dark:text-slate-300"
                    />
                    <div className="relative mt-2">
                        <Lock
                            className="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                            aria-hidden
                        />
                        <TextInput
                            id="password"
                            type={showPassword ? 'text' : 'password'}
                            name="password"
                            value={data.password}
                            placeholder="••••••••"
                            className={`${inputClass} pl-10 pr-11`}
                            autoComplete="current-password"
                            onChange={(e) => setData('password', e.target.value)}
                            aria-invalid={Boolean(errors.password && !authError)}
                        />
                        <button
                            type="button"
                            onClick={() => setShowPassword((v) => !v)}
                            className="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-white/10 dark:hover:text-slate-200"
                            aria-label={showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'}
                            tabIndex={0}
                        >
                            {showPassword ? (
                                <EyeOff className="h-4 w-4" aria-hidden />
                            ) : (
                                <Eye className="h-4 w-4" aria-hidden />
                            )}
                        </button>
                    </div>
                    {!authError ? <InputError message={errors.password} className="mt-1.5" /> : null}
                </div>

                <div className="flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
                    <label className="flex cursor-pointer select-none items-center gap-2.5">
                        <Checkbox
                            name="remember"
                            checked={data.remember}
                            onChange={(e) =>
                                setData('remember', (e.target.checked || false) as false)
                            }
                        />
                        <span className="text-sm text-slate-600 dark:text-slate-400">
                            Recordar sesión
                        </span>
                    </label>
                    {canResetPassword ? (
                        <Link
                            href={route('password.request')}
                            className="text-sm font-semibold text-amber-700 transition hover:text-amber-600 hover:underline dark:text-amber-400"
                        >
                            ¿Olvidaste tu contraseña?
                        </Link>
                    ) : null}
                </div>

                <button
                    type="submit"
                    disabled={processing}
                    className="inline-flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-amber-400 via-amber-400 to-amber-500 text-sm font-bold text-[#071526] shadow-[0_4px_20px_-4px_rgba(245,158,11,0.55)] transition hover:from-amber-300 hover:to-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-55 dark:focus-visible:ring-offset-slate-900"
                >
                    {processing ? (
                        <>
                            <Loader2 className="h-4 w-4 animate-spin" aria-hidden />
                            Ingresando…
                        </>
                    ) : (
                        <>
                            <LogIn className="h-4 w-4" aria-hidden />
                            Ingresar
                            <ArrowRight className="h-4 w-4" aria-hidden />
                        </>
                    )}
                </button>
            </form>

            {isDemoVisible ? (
                <div
                    className="mt-6 rounded-xl border border-dashed border-slate-200/90 bg-slate-50/80 px-3 py-3 dark:border-white/10 dark:bg-slate-950/40"
                    aria-label="Accesos de desarrollo local"
                >
                    <p className="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                        Solo desarrollo local
                    </p>
                    <div className="mt-2 flex flex-wrap gap-1.5">
                        {demoAccounts.map((account) => (
                            <button
                                key={account.label}
                                type="button"
                                onClick={() => fillDemo(account)}
                                className="inline-flex items-center gap-1.5 rounded-lg border border-slate-200/80 bg-white px-2.5 py-1 text-xs font-medium text-slate-700 transition hover:border-amber-400/50 hover:text-amber-800 dark:border-white/10 dark:bg-slate-900/60 dark:text-slate-300 dark:hover:text-amber-300"
                            >
                                <span className="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                    {account.label}
                                </span>
                            </button>
                        ))}
                    </div>
                </div>
            ) : null}

            <div className="mt-8 border-t border-slate-200/70 pt-6 dark:border-white/[0.06]">
                <Link
                    href={route('public.home')}
                    className="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                >
                    <ArrowRight className="h-4 w-4 rotate-180" aria-hidden />
                    Volver al sitio web
                </Link>
            </div>
        </LoginLayout>
    );
}
