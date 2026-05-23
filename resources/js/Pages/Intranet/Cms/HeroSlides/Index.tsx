import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';

type Slide = {
    id: number;
    title: string;
    subtitle: string | null;
    is_active: boolean;
    sort_order: number;
};

type Props = { slides: { data: Slide[] } };

export default function CmsHeroSlidesIndex({ slides }: Props) {
    return (
        <IntranetLayout>
            <Head title="CMS — Hero / Banners" />
            <PageContainer>
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <SectionTitle title="Hero y banners" description="Slides del carrusel principal." />
                    <Link
                        href={route('intranet.cms.hero-slides.create')}
                        className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                    >
                        <Plus className="h-4 w-4" />
                        Nuevo slide
                    </Link>
                </div>
                <Card className="mt-6">
                    <TableContainer>
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b text-left text-plomo">
                                    <th className="px-4 py-3">Título</th>
                                    <th className="px-4 py-3">Orden</th>
                                    <th className="px-4 py-3">Activo</th>
                                    <th className="px-4 py-3" />
                                </tr>
                            </thead>
                            <tbody>
                                {slides.data.map((s) => (
                                    <tr key={s.id} className="border-b">
                                        <td className="px-4 py-3 font-medium">{s.title}</td>
                                        <td className="px-4 py-3">{s.sort_order}</td>
                                        <td className="px-4 py-3">{s.is_active ? 'Sí' : 'No'}</td>
                                        <td className="px-4 py-3 text-right">
                                            <Link
                                                href={route('intranet.cms.hero-slides.edit', s.id)}
                                                className="font-semibold text-navy-800 hover:underline"
                                            >
                                                Editar
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </TableContainer>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
