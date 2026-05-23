import { CmsImagePicker } from '@/Components/Intranet/Cms/CmsImagePicker';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

export default function CmsTestimonialsCreate() {
    const { data, setData, post, processing } = useForm({
        name: '',
        role: '',
        org: '',
        quote: '',
        photo_path: '',
        is_visible: true,
        sort_order: 0,
    });

    return (
        <IntranetLayout>
            <Head title="CMS — Nuevo testimonio" />
            <PageContainer>
                <SectionTitle title="Nuevo testimonio" />
                <Card className="mt-6 max-w-xl p-6">
                    <form
                        onSubmit={(e: FormEvent) => {
                            e.preventDefault();
                            post(route('intranet.cms.testimonials.store'));
                        }}
                        className="space-y-4"
                    >
                        <input
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                            placeholder="Nombre"
                        />
                        <input
                            value={data.role}
                            onChange={(e) => setData('role', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                            placeholder="Rol (madre de familia, egresado…)"
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
                            placeholder="Testimonio"
                        />
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-lg bg-navy-900 px-6 py-2.5 text-sm font-semibold text-white"
                        >
                            Crear
                        </button>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
