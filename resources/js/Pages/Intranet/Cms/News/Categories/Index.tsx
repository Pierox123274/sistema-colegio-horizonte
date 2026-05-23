import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

type Category = { id: number; name: string; slug: string; sort_order: number };

type Props = { categories: Category[] };

export default function CmsNewsCategoriesIndex({ categories }: Props) {
    const { data, setData, post, processing, reset } = useForm({
        name: '',
        slug: '',
        sort_order: 0,
    });

    return (
        <IntranetLayout>
            <Head title="CMS — Categorías de noticias" />
            <PageContainer>
                <SectionTitle title="Categorías" />
                <Card className="mt-6 max-w-md p-6">
                    <form
                        onSubmit={(e: FormEvent) => {
                            e.preventDefault();
                            post(route('intranet.cms.news-categories.store'), {
                                onSuccess: () => reset(),
                            });
                        }}
                        className="space-y-3"
                    >
                        <input
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                            placeholder="Nombre"
                        />
                        <input
                            value={data.slug}
                            onChange={(e) => setData('slug', e.target.value)}
                            className="w-full rounded-lg border-slate-300 font-mono text-sm"
                            placeholder="slug"
                        />
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                        >
                            Agregar categoría
                        </button>
                    </form>
                </Card>
                <ul className="mt-8 space-y-2">
                    {categories.map((c) => (
                        <li
                            key={c.id}
                            className="flex items-center justify-between rounded-lg border border-slate-200 bg-white px-4 py-3"
                        >
                            <span className="font-medium text-navy-900">{c.name}</span>
                            <span className="text-sm text-plomo">{c.slug}</span>
                        </li>
                    ))}
                </ul>
            </PageContainer>
        </IntranetLayout>
    );
}
