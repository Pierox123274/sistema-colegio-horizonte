import { CmsImagePicker } from '@/Components/Intranet/Cms/CmsImagePicker';
import { CmsRichTextEditor } from '@/Components/Intranet/Cms/CmsRichTextEditor';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

type News = {
    id: number;
    category_id: number | null;
    slug: string;
    title: string;
    excerpt: string | null;
    body: string;
    featured_image: string | null;
    is_featured: boolean;
    status: string;
    published_at: string | null;
    meta_title: string | null;
    meta_description: string | null;
    robots_index: boolean;
};

type Props = {
    news: News;
    catalog: {
        statuses: { value: string; label: string }[];
        categories: { id: number; name: string; slug: string }[];
    };
};

export default function CmsNewsEdit({ news, catalog }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        category_id: news.category_id ?? '',
        slug: news.slug,
        title: news.title,
        excerpt: news.excerpt ?? '',
        body: news.body,
        featured_image: news.featured_image ?? '',
        is_featured: news.is_featured,
        status: news.status,
        published_at: news.published_at?.slice(0, 10) ?? '',
        meta_title: news.meta_title ?? '',
        meta_description: news.meta_description ?? '',
        robots_index: news.robots_index,
    });

    const submit = (e: FormEvent) => {
        e.preventDefault();
        put(route('intranet.cms.news.update', news.id));
    };

    return (
        <IntranetLayout>
            <Head title={`Editar — ${news.title}`} />
            <PageContainer>
                <SectionTitle title="Editar noticia" description={news.slug} />
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
                        <div>
                            <label className="text-sm font-medium">Categoría</label>
                            <select
                                value={data.category_id}
                                onChange={(e) => setData('category_id', e.target.value)}
                                className="mt-1 w-full rounded-lg border-slate-300"
                            >
                                <option value="">Sin categoría</option>
                                {catalog.categories.map((c) => (
                                    <option key={c.id} value={c.id}>
                                        {c.name}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <CmsImagePicker
                            label="Imagen destacada"
                            value={data.featured_image}
                            onChange={(path) => setData('featured_image', path)}
                        />
                        <label className="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                checked={data.is_featured}
                                onChange={(e) => setData('is_featured', e.target.checked)}
                            />
                            Marcar como noticia destacada en inicio
                        </label>
                        <div>
                            <label className="text-sm font-medium">Extracto</label>
                            <textarea
                                value={data.excerpt}
                                onChange={(e) => setData('excerpt', e.target.value)}
                                rows={2}
                                className="mt-1 w-full rounded-lg border-slate-300"
                            />
                        </div>
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
                            {processing ? 'Guardando…' : 'Guardar cambios'}
                        </button>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
