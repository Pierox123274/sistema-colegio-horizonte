import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, Link } from '@inertiajs/react';

type PageRow = { id: number; slug: string; title: string; status: string };
type Paginator = { data: PageRow[] };

export default function CmsPagesIndex({ pages }: { pages: Paginator }) {
    return (
        <IntranetLayout>
            <Head title="CMS — Páginas" />
            <PageContainer>
                <SectionTitle title="Páginas" description="Contenido de cada sección del sitio." />
                <Card className="mt-6 divide-y">
                    {pages.data.map((p) => (
                        <div
                            key={p.id}
                            className="flex items-center justify-between px-4 py-3 text-sm"
                        >
                            <span className="font-medium text-navy-900">{p.title}</span>
                            <Link
                                href={route('intranet.cms.pages.edit', p.id)}
                                className="font-semibold text-navy-800 hover:underline"
                            >
                                Editar
                            </Link>
                        </div>
                    ))}
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
