import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';

type Row = { id: number; name: string; role: string; is_visible: boolean };

type Props = { testimonials: { data: Row[] } };

export default function CmsTestimonialsIndex({ testimonials }: Props) {
    return (
        <IntranetLayout>
            <Head title="CMS — Testimonios" />
            <PageContainer>
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <SectionTitle title="Testimonios" />
                    <Link
                        href={route('intranet.cms.testimonials.create')}
                        className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                    >
                        <Plus className="h-4 w-4" />
                        Nuevo
                    </Link>
                </div>
                <Card className="mt-6">
                    <TableContainer>
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b text-left text-plomo">
                                    <th className="px-4 py-3">Nombre</th>
                                    <th className="px-4 py-3">Rol</th>
                                    <th className="px-4 py-3">Visible</th>
                                    <th className="px-4 py-3" />
                                </tr>
                            </thead>
                            <tbody>
                                {testimonials.data.map((t) => (
                                    <tr key={t.id} className="border-b">
                                        <td className="px-4 py-3 font-medium">{t.name}</td>
                                        <td className="px-4 py-3 text-plomo">{t.role}</td>
                                        <td className="px-4 py-3">{t.is_visible ? 'Sí' : 'No'}</td>
                                        <td className="px-4 py-3 text-right">
                                            <Link
                                                href={route('intranet.cms.testimonials.edit', t.id)}
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
