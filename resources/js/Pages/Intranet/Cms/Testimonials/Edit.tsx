import { CmsImagePicker } from '@/Components/Intranet/Cms/CmsImagePicker';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

type Testimonial = {
    id: number;
    name: string;
    role: string;
    org: string | null;
    quote: string;
    photo_path: string | null;
    is_visible: boolean;
    sort_order: number;
};

type Props = { testimonial: Testimonial };

export default function CmsTestimonialsEdit({ testimonial }: Props) {
    const { data, setData, put, processing } = useForm({
        name: testimonial.name,
        role: testimonial.role,
        org: testimonial.org ?? '',
        quote: testimonial.quote,
        photo_path: testimonial.photo_path ?? '',
        is_visible: testimonial.is_visible,
        sort_order: testimonial.sort_order,
    });

    return (
        <IntranetLayout>
            <Head title={`Testimonio — ${testimonial.name}`} />
            <PageContainer>
                <SectionTitle title="Editar testimonio" />
                <Card className="mt-6 max-w-xl p-6">
                    <form
                        onSubmit={(e: FormEvent) => {
                            e.preventDefault();
                            put(route('intranet.cms.testimonials.update', testimonial.id));
                        }}
                        className="space-y-4"
                    >
                        <input
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                        />
                        <input
                            value={data.role}
                            onChange={(e) => setData('role', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                        />
                        <CmsImagePicker
                            label="Foto (opcional)"
                            value={data.photo_path}
                            onChange={(path) => setData('photo_path', path)}
                        />
                        <textarea
                            value={data.quote}
                            onChange={(e) => setData('quote', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                            rows={4}
                        />
                        <label className="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                checked={data.is_visible}
                                onChange={(e) => setData('is_visible', e.target.checked)}
                            />
                            Visible en el sitio
                        </label>
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-lg bg-navy-900 px-6 py-2.5 text-sm font-semibold text-white"
                        >
                            Guardar
                        </button>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
