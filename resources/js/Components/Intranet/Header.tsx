import Dropdown from '@/Components/Dropdown';
import type { User } from '@/types';
import { Bell, ChevronDown, Menu } from 'lucide-react';
import type { ReactNode } from 'react';

function initials(name: string): string {
    return name
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((p) => p[0]?.toUpperCase() ?? '')
        .join('');
}

type HeaderProps = {
    user: User;
    pageTitle: ReactNode;
    onOpenMobileNav: () => void;
    /** Nombre corto del sistema en cabecera */
    systemName?: string;
};

export function Header({
    user,
    pageTitle,
    onOpenMobileNav,
    systemName = 'Sistema Web Institucional',
}: HeaderProps) {
    const primaryRole = user.roles[0] ?? 'Usuario';
    const secondaryRoles = user.roles.slice(1);

    return (
        <header className="sticky top-0 z-30 border-b border-plomo/15 bg-white/95 shadow-sm shadow-navy-900/5 backdrop-blur-md">
            <div className="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <div className="flex min-w-0 flex-1 items-center gap-3">
                    <button
                        type="button"
                        className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-plomo/15 bg-white text-navy-900 shadow-sm transition hover:border-navy-900/20 hover:bg-navy-50 lg:hidden"
                        onClick={onOpenMobileNav}
                        aria-label="Abrir menú"
                    >
                        <Menu className="h-5 w-5" strokeWidth={1.75} />
                    </button>
                    <div className="hidden h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-plomo/10 bg-navy-950 text-xs font-bold text-brand-yellow lg:flex">
                        H
                    </div>
                    <div className="min-w-0">
                        <p className="truncate text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-red">
                            {systemName}
                        </p>
                        <div className="truncate text-base font-bold leading-tight text-navy-900 sm:text-lg">
                            {pageTitle}
                        </div>
                    </div>
                </div>

                <div className="flex shrink-0 items-center gap-2 sm:gap-3">
                    <button
                        type="button"
                        className="hidden h-10 w-10 items-center justify-center rounded-lg border border-plomo/10 text-plomo transition hover:border-navy-900/15 hover:bg-navy-50 hover:text-navy-900 sm:flex"
                        title="Notificaciones (demo)"
                        aria-label="Notificaciones"
                    >
                        <Bell className="h-5 w-5" strokeWidth={1.5} />
                    </button>

                    <div className="hidden flex-col items-end text-right sm:flex">
                        <span className="max-w-[10rem] truncate text-sm font-semibold text-navy-900">
                            {user.name}
                        </span>
                        <span className="max-w-[10rem] truncate text-xs text-plomo">
                            {primaryRole}
                            {secondaryRoles.length > 0
                                ? ` · +${secondaryRoles.length}`
                                : ''}
                        </span>
                    </div>

                    <span
                        className="hidden rounded-full border border-brand-yellow/50 bg-brand-yellow/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-navy-900 sm:inline"
                        title={user.roles.join(', ')}
                    >
                        {primaryRole}
                    </span>

                    <div
                        className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-navy-900 to-navy-950 text-sm font-bold text-white ring-2 ring-brand-yellow/40"
                        title={user.name}
                    >
                        {initials(user.name) || 'U'}
                    </div>

                    <Dropdown>
                        <Dropdown.Trigger>
                            <span className="inline-flex">
                                <button
                                    type="button"
                                    className="inline-flex items-center gap-1 rounded-lg border border-plomo/15 bg-white px-2 py-2 text-plomo shadow-sm transition hover:border-navy-900/20 hover:text-navy-900"
                                    aria-label="Menú de usuario"
                                >
                                    <ChevronDown className="h-4 w-4" strokeWidth={2} />
                                </button>
                            </span>
                        </Dropdown.Trigger>
                        <Dropdown.Content align="right" width="48">
                            <div className="border-b border-gray-100 px-4 py-3 sm:hidden">
                                <p className="truncate text-sm font-semibold text-gray-900">
                                    {user.name}
                                </p>
                                <p className="truncate text-xs text-gray-500">
                                    {user.roles.join(', ')}
                                </p>
                            </div>
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
            </div>
        </header>
    );
}
