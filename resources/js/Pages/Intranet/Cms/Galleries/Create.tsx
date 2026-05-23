import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

export default function CmsGalleriesCreate() {
    const { data, setData, post, processing } = useForm({
        title: '',
        slug: '',
        description: '',
        category: '',
        is_active: true,
        sort_order: 0,
    });

    return (
        <IntranetLayout>
            <Head title="CMS — Nueva galería" />
            <PageContainer>
                <SectionTitle title="Nueva galería" />
                <Card className="mt-6 max-w-xl p-6">
                    <form
                        onSubmit={(e: FormEvent) => {
                            e.preventDefault();
                            post(route('intranet.cms.galleries.store'));
                        }}
                        className="space-y-4"
                    >
                        <input
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                            placeholder="Título"
                        />
                        <input
                            value={data.slug}
                            onChange={(e) => setData('slug', e.target.value)}
                            className="w-full rounded-lg border-slate-300 font-mono text-sm"
                            placeholder="slug"
                        />
                        <textarea
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                            rows={3}
                            placeholder="Descripción"
                        />
                        <input
                            value={data.category}
                            onChange={(e) => setData('category', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                            placeholder="Categoría (campus, eventos…)"
                        />
                        <label className="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                checked={data.is_active}
                                onChange={(e) => setData('is_active', e.target.checked)}
                            />
                            Activa
                        </label>
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-lg bg-navy-900 px-6 py-2.5 text-sm font-semibold text-white"
                        >
                            Crear galería
                        </button>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
