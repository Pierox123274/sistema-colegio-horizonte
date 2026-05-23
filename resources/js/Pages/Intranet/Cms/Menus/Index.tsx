import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, Link } from '@inertiajs/react';

type Props = {
    locations: { value: string; label: string }[];
};

export default function CmsMenusIndex({ locations }: Props) {
    return (
        <IntranetLayout>
            <Head title="CMS — Menús" />
            <PageContainer>
                <SectionTitle title="Menús" description="Navbar y pie de página del sitio público." />
                <div className="mt-8 grid gap-4 sm:grid-cols-2">
                    {locations.map((loc) => (
                        <Link
                            key={loc.value}
                            href={route('intranet.cms.menus.edit', loc.value)}
                            className="block"
                        >
                            <Card className="p-5 transition hover:border-brand-yellow/50">
                                <p className="font-semibold text-navy-900">{loc.label}</p>
                                <p className="mt-1 text-sm text-plomo">{loc.value}</p>
                            </Card>
                        </Link>
                    ))}
                </div>
            </PageContainer>
        </IntranetLayout>
    );
}
