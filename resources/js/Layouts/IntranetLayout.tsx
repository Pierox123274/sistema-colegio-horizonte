import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from '@/Components/Dropdown';
import { PageProps } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { PropsWithChildren, ReactNode, useState } from 'react';

export default function IntranetLayout({
    title,
    children,
}: PropsWithChildren<{ title?: ReactNode }>) {
    const { auth, sidebarNav } = usePage<PageProps>().props;
    const user = auth.user;
    const [sidebarOpen, setSidebarOpen] = useState(false);

    if (!user) {
        return null;
    }

    return (
        <div className="min-h-screen bg-slate-50">
            <div className="lg:flex">
                <aside
                    className={
                        (sidebarOpen ? 'translate-x-0' : '-translate-x-full') +
                        ' fixed inset-y-0 z-40 w-64 border-r border-slate-200 bg-white transition-transform lg:static lg:translate-x-0'
                    }
                >
                    <div className="flex h-16 items-center border-b border-slate-100 px-4">
                        <Link
                            href={route('dashboard')}
                            className="flex items-center gap-2"
                        >
                            <ApplicationLogo className="h-8 w-auto fill-current text-slate-800" />
                            <span className="text-sm font-semibold text-slate-800">
                                Intranet
                            </span>
                        </Link>
                    </div>
                    <nav className="space-y-1 p-3">
                        {sidebarNav.map((item) => (
                            <Link
                                key={item.href + item.label}
                                href={item.href}
                                className="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                            >
                                {item.label}
                            </Link>
                        ))}
                    </nav>
                    <div className="absolute bottom-0 hidden w-64 border-t border-slate-100 p-4 text-xs text-slate-500 lg:block">
                        <p className="font-medium text-slate-600">Roles</p>
                        <p>{user.roles.join(', ') || '—'}</p>
                    </div>
                </aside>

                {sidebarOpen && (
                    <button
                        type="button"
                        className="fixed inset-0 z-30 bg-slate-900/40 lg:hidden"
                        aria-label="Cerrar menú"
                        onClick={() => setSidebarOpen(false)}
                    />
                )}

                <div className="flex min-h-screen flex-1 flex-col">
                    <header className="flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 lg:px-6">
                        <div className="flex items-center gap-3">
                            <button
                                type="button"
                                className="rounded-md p-2 text-slate-600 hover:bg-slate-100 lg:hidden"
                                onClick={() => setSidebarOpen((v) => !v)}
                                aria-label="Abrir menú"
                            >
                                <svg
                                    className="h-6 w-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                </svg>
                            </button>
                            <div className="text-sm font-semibold text-slate-800">
                                {title ?? 'I.E.P. Horizonte'}
                            </div>
                        </div>

                        <div className="relative">
                            <Dropdown>
                                <Dropdown.Trigger>
                                    <span className="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            className="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-800"
                                        >
                                            {user.name}
                                            <svg
                                                className="-me-0.5 ms-2 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fillRule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clipRule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </Dropdown.Trigger>
                                <Dropdown.Content>
                                    <Dropdown.Link href={route('profile.edit')}>
                                        Mi perfil
                                    </Dropdown.Link>
                                    <Dropdown.Link
                                        href={route('logout')}
                                        method="post"
                                        as="button"
                                    >
                                        Cerrar sesión
                                    </Dropdown.Link>
                                </Dropdown.Content>
                            </Dropdown>
                        </div>
                    </header>

                    <main className="flex-1 p-4 lg:p-8">{children}</main>
                </div>
            </div>
        </div>
    );
}
