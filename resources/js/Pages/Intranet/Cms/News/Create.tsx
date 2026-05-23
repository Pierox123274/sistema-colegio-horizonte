import { CmsImagePicker } from '@/Components/Intranet/Cms/CmsImagePicker';
import { CmsRichTextEditor } from '@/Components/Intranet/Cms/CmsRichTextEditor';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

type Props = {
    catalog: {
        statuses: { value: string; label: string }[];
        categories: { id: number; name: string; slug: string }[];
    };
};

export default function CmsNewsCreate({ catalog }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        category_id: '',
        slug: '',
        title: '',
        excerpt: '',
        body: '<p></p>',
        featured_image: '',
        is_featured: false,
        status: 'draft',
        published_at: '',
        meta_title: '',
        meta_description: '',
        robots_index: true,
    });

    const submit = (e: FormEvent) => {
        e.preventDefault();
        post(route('intranet.cms.news.store'));
    };

    return (
        <IntranetLayout>
            <Head title="CMS — Nueva noticia" />
            <PageContainer>
                <SectionTitle title="Nueva noticia" />
                <Card className="mt-6 max-w-3xl p-6">
                    <form onSubmit={submit} className="space-y-5">
                        <div>
                            <label className="text-sm font-medium">Título</label>
                            <input
                                value={data.title}
                                onChange={(e) => setData('title', e.target.value)}
                                className="mt-1 w-full rounded-lg border-slate-300"
                            />
                            {errors.title ? (
                                <p className="text-sm text-red-600">{errors.title}</p>
                            ) : null}
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label className="text-sm font-medium">Slug</label>
                                <input
                                    value={data.slug}
                                    onChange={(e) => setData('slug', e.target.value)}
                                    className="mt-1 w-full rounded-lg border-slate-300 font-mono text-sm"
                                />
                            </div>
                            <div>
                                <label className="text-sm font-medium">Estado</label>
                                <select
                                    value={data.status}
                                    onChange={(e) => setData('status', e.target.value)}
                                    className="mt-1 w-full rounded-lg border-slate-300"
                                >
                                    {catalog.statuses.map((s) => (
                                        <option key={s.value} value={s.value}>
                                            {s.label}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>
                        <CmsImagePicker
                            label="Imagen destacada"
                            value={data.featured_image}
                            onChange={(path) => setData('featured_image', path)}
                        />
                        <CmsRichTextEditor
                            label="Contenido"
                            value={data.body}
                            onChange={(html) => setData('body', html)}
                            error={errors.body}
                        />
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-lg bg-navy-900 px-6 py-2.5 text-sm font-semibold text-white disabled:opacity-50"
                        >
                            {processing ? 'Creando…' : 'Crear noticia'}
                        </button>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
