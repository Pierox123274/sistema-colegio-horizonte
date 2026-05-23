import { CmsMediaLibrary } from '@/Components/Intranet/Cms/CmsMediaLibrary';
import { CmsUploadDropzone } from '@/Components/Intranet/Cms/CmsUploadDropzone';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { cmsStorageUrl } from '@/lib/cmsMedia';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, router, useForm } from '@inertiajs/react';
import { ArrowDown, ArrowUp, Eye, EyeOff, Star, Trash2 } from 'lucide-react';
import { FormEvent, useState } from 'react';

type GalleryImage = {
    id: number;
    image_path: string;
    caption: string | null;
    sort_order: number;
    is_active: boolean;
};

type Gallery = {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    category: string | null;
    is_active: boolean;
    sort_order: number;
    images: GalleryImage[];
};

type Props = { gallery: Gallery };

export default function CmsGalleriesEdit({ gallery }: Props) {
    const [libraryOpen, setLibraryOpen] = useState(false);
    const { data, setData, put, processing } = useForm({
        title: gallery.title,
        slug: gallery.slug,
        description: gallery.description ?? '',
        category: gallery.category ?? '',
        is_active: gallery.is_active,
        sort_order: gallery.sort_order,
    });

    const sortedImages = [...gallery.images].sort((a, b) => a.sort_order - b.sort_order);

    const reorder = (ids: number[]) => {
        router.post(route('intranet.cms.galleries.images.reorder', gallery.id), { order: ids });
    };

    const moveImage = (index: number, direction: -1 | 1) => {
        const next = [...sortedImages];
        const target = index + direction;
        if (target < 0 || target >= next.length) return;
        [next[index], next[target]] = [next[target], next[index]];
        reorder(next.map((img) => img.id));
    };

    const toggleActive = (image: GalleryImage) => {
        router.patch(route('intranet.cms.galleries.images.update', [gallery.id, image.id]), {
            is_active: !image.is_active,
        });
    };

    const setCover = (image: GalleryImage) => {
        const ids = [image.id, ...sortedImages.filter((i) => i.id !== image.id).map((i) => i.id)];
        reorder(ids);
    };

    const uploadFiles = (files: FileList) => {
        const file = files[0];
        if (!file) return;
        router.post(
            route('intranet.cms.galleries.images.store', gallery.id),
            { file },
            { forceFormData: true },
        );
    };

    const addFromLibrary = (path: string) => {
        router.post(route('intranet.cms.galleries.images.store', gallery.id), {
            images: [{ image_path: path, sort_order: sortedImages.length }],
        });
    };

    return (
        <IntranetLayout>
            <Head title={`Galería — ${gallery.title}`} />
            <PageContainer>
                <SectionTitle title="Editar galería" description={gallery.slug} />
                <Card className="mt-6 max-w-xl p-6">
                    <form
                        onSubmit={(e: FormEvent) => {
                            e.preventDefault();
                            put(route('intranet.cms.galleries.update', gallery.id));
                        }}
                        className="space-y-4"
                    >
                        <input
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                        />
                        <input
                            value={data.slug}
                            onChange={(e) => setData('slug', e.target.value)}
                            className="w-full rounded-lg border-slate-300 font-mono text-sm"
                        />
                        <textarea
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                            className="w-full rounded-lg border-slate-300"
                            rows={3}
                        />
                        <label className="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                checked={data.is_active}
                                onChange={(e) => setData('is_active', e.target.checked)}
                            />
                            Galería activa en el sitio
                        </label>
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-lg bg-navy-900 px-6 py-2.5 text-sm font-semibold text-white"
                        >
                            Guardar datos
                        </button>
                    </form>
                </Card>

                <Card className="mt-8 p-6">
                    <div className="flex flex-wrap items-center justify-between gap-3">
                        <h3 className="font-semibold text-navy-900">Imágenes del álbum</h3>
                        <button
                            type="button"
                            onClick={() => setLibraryOpen(true)}
                            className="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium"
                        >
                            Elegir desde medios
                        </button>
                    </div>
                    <div className="mt-4">
                        <CmsUploadDropzone onFiles={uploadFiles} />
                    </div>
                    <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {sortedImages.map((img, index) => {
                            const url = cmsStorageUrl(img.image_path);
                            const isCover = index === 0;

                            return (
                                <div
                                    key={img.id}
                                    className={`overflow-hidden rounded-xl border bg-white shadow-sm ${
                                        !img.is_active ? 'opacity-60' : ''
                                    } ${isCover ? 'ring-2 ring-brand-yellow/50' : ''}`}
                                >
                                    {url ? (
                                        <img
                                            src={url}
                                            alt={img.caption ?? ''}
                                            className="aspect-video w-full object-cover"
                                        />
                                    ) : null}
                                    <div className="flex items-center justify-between gap-1 border-t border-slate-100 p-2">
                                        <div className="flex gap-1">
                                            <button
                                                type="button"
                                                title="Subir orden"
                                                onClick={() => moveImage(index, -1)}
                                                className="rounded p-1 hover:bg-slate-100"
                                            >
                                                <ArrowUp className="h-4 w-4" />
                                            </button>
                                            <button
                                                type="button"
                                                title="Bajar orden"
                                                onClick={() => moveImage(index, 1)}
                                                className="rounded p-1 hover:bg-slate-100"
                                            >
                                                <ArrowDown className="h-4 w-4" />
                                            </button>
                                            <button
                                                type="button"
                                                title="Portada"
                                                onClick={() => setCover(img)}
                                                className="rounded p-1 hover:bg-slate-100"
                                            >
                                                <Star
                                                    className={`h-4 w-4 ${isCover ? 'fill-amber-500 text-amber-500' : ''}`}
                                                />
                                            </button>
                                        </div>
                                        <div className="flex gap-1">
                                            <button
                                                type="button"
                                                onClick={() => toggleActive(img)}
                                                className="rounded p-1 hover:bg-slate-100"
                                                title={img.is_active ? 'Ocultar' : 'Mostrar'}
                                            >
                                                {img.is_active ? (
                                                    <Eye className="h-4 w-4" />
                                                ) : (
                                                    <EyeOff className="h-4 w-4 text-plomo" />
                                                )}
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => {
                                                    if (confirm('¿Eliminar imagen?')) {
                                                        router.delete(
                                                            route(
                                                                'intranet.cms.galleries.images.destroy',
                                                                [gallery.id, img.id],
                                                            ),
                                                        );
                                                    }
                                                }}
                                                className="rounded p-1 text-red-700 hover:bg-red-50"
                                            >
                                                <Trash2 className="h-4 w-4" />
                                            </button>
                                        </div>
                                    </div>
                                    {isCover ? (
                                        <p className="bg-amber-50 px-2 py-1 text-center text-[10px] font-bold uppercase text-amber-800">
                                            Portada
                                        </p>
                                    ) : null}
                                </div>
                            );
                        })}
                    </div>
                </Card>
            </PageContainer>
            <CmsMediaLibrary
                open={libraryOpen}
                onClose={() => setLibraryOpen(false)}
                onSelect={(item) => addFromLibrary(item.path)}
            />
        </IntranetLayout>
    );
}
