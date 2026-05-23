import { CmsRichTextEditor } from '@/Components/Intranet/Cms/CmsRichTextEditor';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

type Props = {
    catalog: { statuses: { value: string; label: string }[] };
};

export default function CmsPagesCreate({ catalog }: Props) {
    const { data, setData, post, processing } = useForm({
        slug: '',
        title: '',
        subtitle: '',
        hero_title: '',
        hero_subtitle: '',
        body: '',
        status: 'draft',
        meta_title: '',
        meta_description: '',
        robots_index: true,
    });

    return (
        <IntranetLayout>
            <Head title="CMS — Nueva página" />
            <PageContainer>
                <SectionTitle title="Nueva página" />
                <Card className="mt-6 max-w-3xl p-6">
                    <form
                        onSubmit={(e: FormEvent) => {
                            e.preventDefault();
                            post(route('intranet.cms.pages.store'));
                        }}
                        className="space-y-4"
                    >
                        <input
                            value={data.slug}
                            onChange={(e) => setData('slug', e.target.value)}
                            className="w-full rounded-lg border-slate-300 font-mono text-sm"
                            placeholder="slug-url"
                        />
                        <input
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                            placeholder="Título"
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
                            className="rounded-lg bg-navy-900 px-6 py-2.5 text-sm font-semibold text-white disabled:opacity-50"
                        >
                            Crear página
                        </button>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
