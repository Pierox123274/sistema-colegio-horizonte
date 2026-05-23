import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';

type GalleryRow = {
    id: number;
    title: string;
    slug: string;
    category: string | null;
    is_active: boolean;
};

type Props = {
    galleries: { data: GalleryRow[] };
};

export default function CmsGalleriesIndex({ galleries }: Props) {
    return (
        <IntranetLayout>
            <Head title="CMS — Galerías" />
            <PageContainer>
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <SectionTitle title="Galerías" description="Álbumes del sitio público." />
                    <Link
                        href={route('intranet.cms.galleries.create')}
                        className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                    >
                        <Plus className="h-4 w-4" />
                        Nueva galería
                    </Link>
                </div>
                <Card className="mt-6">
                    <TableContainer>
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b text-left text-plomo">
                                    <th className="px-4 py-3">Título</th>
                                    <th className="px-4 py-3">Categoría</th>
                                    <th className="px-4 py-3">Estado</th>
                                    <th className="px-4 py-3" />
                                </tr>
                            </thead>
                            <tbody>
                                {galleries.data.map((g) => (
                                    <tr key={g.id} className="border-b border-slate-100">
                                        <td className="px-4 py-3 font-medium">{g.title}</td>
                                        <td className="px-4 py-3 text-plomo">{g.category ?? '—'}</td>
                                        <td className="px-4 py-3">
                                            {g.is_active ? 'Activa' : 'Inactiva'}
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <Link
                                                href={route('intranet.cms.galleries.edit', g.id)}
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
