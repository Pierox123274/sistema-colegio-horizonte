import { Header } from '@/Components/Intranet/Header';
import { Sidebar } from '@/Components/Intranet/Sidebar';
import { PageProps } from '@/types';
import { usePage } from '@inertiajs/react';
import { PropsWithChildren, ReactNode, useEffect, useState } from 'react';

const STORAGE_KEY = 'intranet.sidebar.collapsed';

export default function IntranetLayout({
    title,
    children,
}: PropsWithChildren<{ title?: ReactNode }>) {
    const { auth, sidebarNav, intranetHomeHref } = usePage<PageProps>().props;
    const user = auth.user;
    const [collapsed, setCollapsed] = useState(false);
    const [mobileOpen, setMobileOpen] = useState(false);

    useEffect(() => {
        if (typeof window === 'undefined') {
            return;
        }
        setCollapsed(window.localStorage.getItem(STORAGE_KEY) === '1');
    }, []);

    const toggleCollapsed = () => {
        setCollapsed((prev) => {
            const next = !prev;
            window.localStorage.setItem(STORAGE_KEY, next ? '1' : '0');
            return next;
        });
    };

    if (!user) {
        return null;
    }

    const primaryRole = user.roles[0] ?? 'Sin rol';
    const mainMargin =
        collapsed && !mobileOpen ? 'lg:ml-[4.5rem]' : 'lg:ml-64';

    return (
        <div className="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100/70 dark:from-slate-950 dark:to-slate-950">
            {mobileOpen && (
                <button
                    type="button"
                    className="fixed inset-0 z-40 bg-navy-950/60 backdrop-blur-[1px] lg:hidden"
                    aria-label="Cerrar menú lateral"
                    onClick={() => setMobileOpen(false)}
                />
            )}

            <Sidebar
                items={sidebarNav}
                collapsed={collapsed}
                onToggleCollapse={toggleCollapsed}
                mobileOpen={mobileOpen}
                onMobileClose={() => setMobileOpen(false)}
                primaryRole={primaryRole}
                homeHref={intranetHomeHref}
            />

            <div
                className={`flex min-h-screen flex-col transition-[margin] duration-200 ease-out ${mainMargin}`}
            >
                <Header
                    user={user}
                    pageTitle={title ?? 'Panel'}
                    onOpenMobileNav={() => setMobileOpen(true)}
                />
                <main className="flex-1">{children}</main>
            </div>
        </div>
    );
}
