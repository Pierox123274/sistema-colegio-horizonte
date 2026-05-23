import { CmsImagePicker } from '@/Components/Intranet/Cms/CmsImagePicker';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

type Slide = {
    id: number;
    title: string;
    subtitle: string | null;
    image_path: string | null;
    badge: string | null;
    cta_primary_label: string | null;
    cta_primary_url: string | null;
    cta_secondary_label: string | null;
    cta_secondary_url: string | null;
    is_active: boolean;
    sort_order: number;
};

type Props = { slide: Slide };

export default function CmsHeroSlidesEdit({ slide }: Props) {
    const { data, setData, put, processing } = useForm({
        title: slide.title,
        subtitle: slide.subtitle ?? '',
        image_path: slide.image_path ?? '',
        badge: slide.badge ?? '',
        cta_primary_label: slide.cta_primary_label ?? '',
        cta_primary_url: slide.cta_primary_url ?? '',
        cta_secondary_label: slide.cta_secondary_label ?? '',
        cta_secondary_url: slide.cta_secondary_url ?? '',
        is_active: slide.is_active,
        sort_order: slide.sort_order,
    });

    return (
        <IntranetLayout>
            <Head title={`Slide — ${slide.title}`} />
            <PageContainer>
                <SectionTitle title="Editar slide" />
                <Card className="mt-6 max-w-xl p-6">
                    <form
                        onSubmit={(e: FormEvent) => {
                            e.preventDefault();
                            put(route('intranet.cms.hero-slides.update', slide.id));
                        }}
                        className="space-y-4"
                    >
                        <input
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                        />
                        <input
                            value={data.subtitle}
                            onChange={(e) => setData('subtitle', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                        />
                        <CmsImagePicker
                            label="Imagen del banner"
                            value={data.image_path}
                            onChange={(path) => setData('image_path', path)}
                        />
                        <label className="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                checked={data.is_active}
                                onChange={(e) => setData('is_active', e.target.checked)}
                            />
                            Activo
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
