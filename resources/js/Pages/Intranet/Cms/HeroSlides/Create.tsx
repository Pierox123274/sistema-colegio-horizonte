import { CmsImagePicker } from '@/Components/Intranet/Cms/CmsImagePicker';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

export default function CmsHeroSlidesCreate() {
    const { data, setData, post, processing } = useForm({
        title: '',
        subtitle: '',
        image_path: '',
        badge: '',
        cta_primary_label: '',
        cta_primary_url: '',
        cta_secondary_label: '',
        cta_secondary_url: '',
        is_active: true,
        sort_order: 0,
    });

    return (
        <IntranetLayout>
            <Head title="CMS — Nuevo slide" />
            <PageContainer>
                <SectionTitle title="Nuevo slide hero" />
                <Card className="mt-6 max-w-xl p-6">
                    <form
                        onSubmit={(e: FormEvent) => {
                            e.preventDefault();
                            post(route('intranet.cms.hero-slides.store'));
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
                            value={data.subtitle}
                            onChange={(e) => setData('subtitle', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                            placeholder="Subtítulo"
                        />
                        <CmsImagePicker
                            label="Imagen del banner"
                            value={data.image_path}
                            onChange={(path) => setData('image_path', path)}
                        />
                        <div className="grid gap-3 sm:grid-cols-2">
                            <input
                                value={data.cta_primary_label}
                                onChange={(e) => setData('cta_primary_label', e.target.value)}
                                className="rounded-lg border-slate-300"
                                placeholder="CTA principal"
                            />
                            <input
                                value={data.cta_primary_url}
                                onChange={(e) => setData('cta_primary_url', e.target.value)}
                                className="rounded-lg border-slate-300"
                                placeholder="URL CTA"
                            />
                        </div>
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-lg bg-navy-900 px-6 py-2.5 text-sm font-semibold text-white"
                        >
                            Crear slide
                        </button>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
