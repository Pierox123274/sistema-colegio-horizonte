import { CmsMediaLibrary } from '@/Components/Intranet/Cms/CmsMediaLibrary';
import { CmsUploadDropzone } from '@/Components/Intranet/Cms/CmsUploadDropzone';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { copyToClipboard, type CmsMediaItem } from '@/lib/cmsMedia';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, router } from '@inertiajs/react';
import { Check, Copy, ExternalLink, Search, Trash2 } from 'lucide-react';
import { FormEvent, useState } from 'react';

type Props = {
    media: {
        data: CmsMediaItem[];
        meta: { total: number };
    };
    filters: { search?: string };
};

export default function CmsMediaIndex({ media, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [preview, setPreview] = useState<CmsMediaItem | null>(media.data[0] ?? null);
    const [copied, setCopied] = useState(false);
    const [pickerOpen, setPickerOpen] = useState(false);

    const applySearch = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('intranet.cms.media.index'),
            { search: search || undefined },
            { preserveState: true, replace: true },
        );
    };

    const upload = (files: FileList) => {
        const file = files[0];
        if (!file) return;
        router.post(route('intranet.cms.media.store'), { file }, { forceFormData: true });
    };

    const destroy = (id: number) => {
        if (!confirm('¿Eliminar este archivo del servidor?')) return;
        router.delete(route('intranet.cms.media.destroy', id));
    };

    return (
        <IntranetLayout>
            <Head title="CMS — Medios" />
            <PageContainer>
                <SectionTitle
                    title="Biblioteca de medios"
                    description={`${media.meta.total} archivos · Imágenes del sitio público`}
                />

                <Card className="mt-6 p-4">
                    <form onSubmit={applySearch} className="flex flex-wrap gap-3">
                        <div className="relative min-w-[200px] flex-1">
                            <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-plomo" />
                            <input
                                type="search"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Buscar…"
                                className="w-full rounded-lg border-slate-300 py-2 pl-9 text-sm"
                            />
                        </div>
                        <button
                            type="submit"
                            className="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium"
                        >
                            Buscar
                        </button>
                    </form>
                    <div className="mt-4">
                        <CmsUploadDropzone onFiles={upload} />
                    </div>
                </Card>

                <div className="mt-8 grid gap-6 lg:grid-cols-[1fr_300px]">
                    <div className="grid gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
                        {media.data.map((item) => (
                            <button
                                key={item.id}
                                type="button"
                                onClick={() => setPreview(item)}
                                className={`overflow-hidden rounded-xl border bg-white text-left shadow-sm transition hover:shadow-md ${
                                    preview?.id === item.id
                                        ? 'border-brand-yellow ring-2 ring-brand-yellow/30'
                                        : 'border-slate-200'
                                }`}
                            >
                                <img
                                    src={item.url ?? ''}
                                    alt={item.alt ?? ''}
                                    className="aspect-square w-full object-cover"
                                />
                                <div className="p-3">
                                    <p className="truncate text-sm font-medium text-navy-900">
                                        {item.filename}
                                    </p>
                                    <p className="text-xs text-plomo">
                                        {item.mime} · {item.size_label}
                                    </p>
                                </div>
                            </button>
                        ))}
                    </div>

                    <Card className="sticky top-4 h-fit p-5">
                        {preview ? (
                            <>
                                <img
                                    src={preview.url ?? ''}
                                    alt=""
                                    className="w-full rounded-lg object-cover"
                                />
                                <h3 className="mt-4 font-semibold text-navy-900">
                                    {preview.filename}
                                </h3>
                                <p className="mt-1 text-xs text-plomo break-all">{preview.path}</p>
                                <div className="mt-4 flex flex-col gap-2">
                                    <button
                                        type="button"
                                        onClick={async () => {
                                            if (preview.url && (await copyToClipboard(preview.url))) {
                                                setCopied(true);
                                                setTimeout(() => setCopied(false), 2000);
                                            }
                                        }}
                                        className="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium"
                                    >
                                        {copied ? (
                                            <Check className="h-4 w-4 text-emerald-600" />
                                        ) : (
                                            <Copy className="h-4 w-4" />
                                        )}
                                        Copiar URL
                                    </button>
                                    {preview.url ? (
                                        <a
                                            href={preview.url}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium"
                                        >
                                            <ExternalLink className="h-4 w-4" />
                                            Abrir
                                        </a>
                                    ) : null}
                                    <button
                                        type="button"
                                        onClick={() => destroy(preview.id)}
                                        className="inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-800"
                                    >
                                        <Trash2 className="h-4 w-4" />
                                        Eliminar
                                    </button>
                                </div>
                            </>
                        ) : (
                            <p className="text-sm text-plomo">Selecciona un archivo de la cuadrícula.</p>
                        )}
                    </Card>
                </div>
            </PageContainer>
            <CmsMediaLibrary open={pickerOpen} onClose={() => setPickerOpen(false)} onSelect={() => {}} />
        </IntranetLayout>
    );
}
