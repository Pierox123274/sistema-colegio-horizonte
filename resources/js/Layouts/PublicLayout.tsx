import { PublicFooter } from '@/Components/Public/PublicFooter';
import { PublicNavbar } from '@/Components/Public/PublicNavbar';
import { PublicPageFade } from '@/Components/Public/Premium/PublicPageFade';
import { PublicThemeProvider } from '@/Components/Public/Premium/PublicThemeProvider';
import { Head } from '@inertiajs/react';
import { PropsWithChildren } from 'react';

type PublicLayoutProps = PropsWithChildren<{
    title: string;
    description?: string;
}>;

export default function PublicLayout({
    title,
    description = 'I.E.P. Horizonte — colegio privado de excelencia en Inicial, Primaria y Secundaria.',
    children,
}: PublicLayoutProps) {
    return (
        <PublicThemeProvider>
            <Head>
                <title>{title}</title>
                <meta name="description" content={description} />
            </Head>
            <div className="flex min-h-screen flex-col scroll-smooth bg-institutional-surface text-slate-900 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100">
                <PublicNavbar />
                <main className="flex-1">
                    <PublicPageFade>{children}</PublicPageFade>
                </main>
                <PublicFooter />
            </div>
        </PublicThemeProvider>
    );
}
