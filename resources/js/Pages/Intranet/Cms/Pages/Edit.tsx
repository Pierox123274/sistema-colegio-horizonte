import { CmsImagePicker } from '@/Components/Intranet/Cms/CmsImagePicker';
import { CmsRichTextEditor } from '@/Components/Intranet/Cms/CmsRichTextEditor';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

type CmsPage = {
    id: number;
    slug: string;
    title: string;
    subtitle: string | null;
    hero_title: string | null;
    hero_subtitle: string | null;
    hero_image: string | null;
    body: string | null;
    status: string;
    meta_title: string | null;
    meta_description: string | null;
    robots_index: boolean;
};

type Props = {
    page: CmsPage;
    catalog: { statuses: { value: string; label: string }[] };
};

export default function CmsPagesEdit({ page, catalog }: Props) {
    const { data, setData, put, processing } = useForm({
        slug: page.slug,
        title: page.title,
        subtitle: page.subtitle ?? '',
        hero_title: page.hero_title ?? '',
        hero_subtitle: page.hero_subtitle ?? '',
        hero_image: page.hero_image ?? '',
        body: page.body ?? '',
        status: page.status,
        meta_title: page.meta_title ?? '',
        meta_description: page.meta_description ?? '',
        robots_index: page.robots_index,
        published_at: '',
    });

    return (
        <IntranetLayout>
            <Head title={`Página — ${page.title}`} />
            <PageContainer>
                <SectionTitle title="Editar página" description={page.slug} />
                <Card className="mt-6 max-w-3xl p-6">
                    <form
                        onSubmit={(e: FormEvent) => {
                            e.preventDefault();
                            put(route('intranet.cms.pages.update', page.id));
                        }}
                        className="space-y-4"
                    >
                        <input
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                            placeholder="Título"
                        />
                        <CmsImagePicker
                            label="Imagen hero de la página"
                            value={data.hero_image}
                            onChange={(path) => setData('hero_image', path)}
                            hint="Se muestra en el encabezado público de esta sección."
                        />
                        <CmsRichTextEditor
                            label="Contenido"
                            value={data.body}
                            onChange={(html) => setData('body', html)}
                        />
                        <select
                            value={data.status}
                            onChange={(e) => setData('status', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                        >
                            {catalog.statuses.map((s) => (
                                <option key={s.value} value={s.value}>
                                    {s.label}
                                </option>
                            ))}
                        </select>
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-lg bg-navy-900 px-6 py-2 text-sm font-semibold text-white"
                        >
                            Guardar
                        </button>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
