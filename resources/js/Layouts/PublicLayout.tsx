import { PublicFooter } from '@/Components/Public/PublicFooter';
import { PublicNavbar } from '@/Components/Public/PublicNavbar';
import { PublicPageFade } from '@/Components/Public/Premium/PublicPageFade';
import { PublicThemeProvider } from '@/Components/Public/Premium/PublicThemeProvider';
import type { PageProps } from '@/types';
import type { CmsSeo, CmsSettings } from '@/types/cms';
import { Head, usePage } from '@inertiajs/react';
import { PropsWithChildren } from 'react';

type PublicLayoutProps = PropsWithChildren<{
    title: string;
    description?: string;
}>;

type LayoutPageProps = PageProps<{
    seo?: CmsSeo;
    cmsSettings?: CmsSettings;
}>;

export default function PublicLayout({
    title,
    description = 'I.E.P. Horizonte — colegio privado de excelencia en Inicial, Primaria y Secundaria.',
    children,
}: PublicLayoutProps) {
    const page = usePage<LayoutPageProps>();
    const { seo } = page.props;
    const { cmsSettings } = page.props;
    const pageTitle = seo?.title ?? title;
    const pageDescription = seo?.description ?? description;
    const baseUrl = (import.meta.env.VITE_APP_URL as string | undefined) ?? '';
    const canonical = baseUrl ? `${baseUrl.replace(/\/$/, '')}${page.url}` : undefined;

    return (
        <PublicThemeProvider>
            <Head>
                <title>{pageTitle}</title>
                <meta name="description" content={pageDescription} />
                {canonical ? <link rel="canonical" href={canonical} /> : null}
                {cmsSettings?.faviconUrl ? (
                    <link rel="icon" href={cmsSettings.faviconUrl} />
                ) : null}
                <meta property="og:title" content={pageTitle} />
                <meta property="og:description" content={pageDescription} />
                <meta property="og:type" content="website" />
                {canonical ? <meta property="og:url" content={canonical} /> : null}
                {seo?.image ? <meta property="og:image" content={seo.image} /> : null}
                {seo?.robotsIndex === false ? (
                    <meta name="robots" content="noindex,nofollow" />
                ) : null}
                <link rel="manifest" href="/manifest.webmanifest" />
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
