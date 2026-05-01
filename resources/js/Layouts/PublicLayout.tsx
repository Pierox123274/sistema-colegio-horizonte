import { PublicFooter } from '@/Components/Public/PublicFooter';
import { PublicNavbar } from '@/Components/Public/PublicNavbar';
import { Head } from '@inertiajs/react';
import { PropsWithChildren } from 'react';

type PublicLayoutProps = PropsWithChildren<{
    title: string;
    description?: string;
}>;

export default function PublicLayout({
    title,
    description = 'I.E.P. Horizonte — colegio privado con excelencia en Inicial, Primaria y Secundaria.',
    children,
}: PublicLayoutProps) {
    return (
        <>
            <Head>
                <title>{title}</title>
                <meta name="description" content={description} />
            </Head>
            <div className="flex min-h-screen flex-col bg-navy-50">
                <PublicNavbar />
                <main className="flex-1">{children}</main>
                <PublicFooter />
            </div>
        </>
    );
}
