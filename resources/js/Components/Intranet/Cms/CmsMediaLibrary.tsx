import { CmsUploadDropzone } from '@/Components/Intranet/Cms/CmsUploadDropzone';
import { copyToClipboard, type CmsMediaItem } from '@/lib/cmsMedia';
import { Check, Copy, Loader2, Search, X } from 'lucide-react';
import { useCallback, useEffect, useState } from 'react';

type Props = {
    open: boolean;
    onClose: () => void;
    onSelect: (item: CmsMediaItem) => void;
    title?: string;
};

export function CmsMediaLibrary({
    open,
    onClose,
    onSelect,
    title = 'Biblioteca de medios',
}: Props) {
    const [items, setItems] = useState<CmsMediaItem[]>([]);
    const [loading, setLoading] = useState(false);
    const [uploading, setUploading] = useState(false);
    const [search, setSearch] = useState('');
    const [preview, setPreview] = useState<CmsMediaItem | null>(null);
    const [copied, setCopied] = useState(false);

    const load = useCallback(async (q: string) => {
        setLoading(true);
        try {
            const params = new URLSearchParams({ mime: 'image', per_page: '48' });
            if (q) {
                params.set('search', q);
            }
            const res = await fetch(`${route('intranet.cms.media.browse')}?${params}`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });
            const json = (await res.json()) as { data: CmsMediaItem[] };
            setItems(json.data ?? []);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        if (open) {
            load(search);
            setPreview(null);
        }
    }, [open, load, search]);

    const uploadFile = async (file: File) => {
        setUploading(true);
        const body = new FormData();
        body.append('file', file);
        try {
            const res = await fetch(route('intranet.cms.media.store'), {
                method: 'POST',
                body,
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN':
                        document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
                            ?.content ?? '',
                },
                credentials: 'same-origin',
            });
            const json = (await res.json()) as { media: CmsMediaItem };
            if (json.media) {
                setItems((prev) => [json.media, ...prev]);
                setPreview(json.media);
            }
        } finally {
            setUploading(false);
        }
    };

    const handleCopy = async (item: CmsMediaItem) => {
        const url = item.url ?? '';
        if (await copyToClipboard(url)) {
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        }
    };

    if (!open) {
        return null;
    }

    return (
        <div className="fixed inset-0 z-[80] flex items-center justify-center p-4">
            <button
                type="button"
                className="absolute inset-0 bg-navy-950/60 backdrop-blur-sm"
                aria-label="Cerrar"
                onClick={onClose}
            />
            <div className="relative flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div className="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <h2 className="text-lg font-bold text-navy-900">{title}</h2>
                    <button
                        type="button"
                        onClick={onClose}
                        className="rounded-lg p-2 text-plomo hover:bg-slate-100"
                    >
                        <X className="h-5 w-5" />
                    </button>
                </div>

                <div className="grid flex-1 gap-0 overflow-hidden lg:grid-cols-[1fr_280px]">
                    <div className="flex flex-col overflow-hidden p-4">
                        <div className="mb-4 flex gap-2">
                            <div className="relative flex-1">
                                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-plomo" />
                                <input
                                    type="search"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    placeholder="Buscar por nombre…"
                                    className="w-full rounded-lg border-slate-300 py-2 pl-9 text-sm"
                                />
                            </div>
                        </div>

                        <CmsUploadDropzone
                            disabled={uploading}
                            onFiles={(files) => {
                                Array.from(files).forEach((f) => uploadFile(f));
                            }}
                        />

                        <div className="mt-4 flex-1 overflow-y-auto">
                            {loading ? (
                                <div className="flex justify-center py-12">
                                    <Loader2 className="h-8 w-8 animate-spin text-navy-700" />
                                </div>
                            ) : (
                                <div className="grid gap-3 sm:grid-cols-3 md:grid-cols-4">
                                    {items.map((item) => (
                                        <button
                                            key={item.id}
                                            type="button"
                                            onClick={() => setPreview(item)}
                                            className={`group overflow-hidden rounded-xl border text-left transition ${
                                                preview?.id === item.id
                                                    ? 'border-brand-yellow ring-2 ring-brand-yellow/40'
                                                    : 'border-slate-200 hover:border-navy-300'
                                            }`}
                                        >
                                            <img
                                                src={item.url ?? ''}
                                                alt={item.alt ?? item.filename}
                                                className="aspect-square w-full object-cover"
                                            />
                                            <div className="p-2">
                                                <p className="truncate text-xs font-medium text-navy-900">
                                                    {item.filename}
                                                </p>
                                                <p className="text-[10px] text-plomo">{item.size_label}</p>
                                            </div>
                                        </button>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>

                    <aside className="flex flex-col border-l border-slate-200 bg-slate-50 p-4">
                        {preview ? (
                            <>
                                <img
                                    src={preview.url ?? ''}
                                    alt=""
                                    className="w-full rounded-lg object-cover"
                                />
                                <p className="mt-3 text-sm font-semibold text-navy-900">
                                    {preview.filename}
                                </p>
                                <p className="text-xs text-plomo">
                                    {preview.mime} · {preview.size_label}
                                </p>
                                <button
                                    type="button"
                                    onClick={() => handleCopy(preview)}
                                    className="mt-3 inline-flex items-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-xs font-medium"
                                >
                                    {copied ? (
                                        <Check className="h-3.5 w-3.5 text-emerald-600" />
                                    ) : (
                                        <Copy className="h-3.5 w-3.5" />
                                    )}
                                    Copiar URL
                                </button>
                                <button
                                    type="button"
                                    onClick={() => {
                                        onSelect(preview);
                                        onClose();
                                    }}
                                    className="mt-4 w-full rounded-lg bg-navy-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-navy-800"
                                >
                                    Usar esta imagen
                                </button>
                            </>
                        ) : (
                            <p className="text-sm text-plomo">
                                Selecciona una imagen de la cuadrícula o sube una nueva.
                            </p>
                        )}
                    </aside>
                </div>
            </div>
        </div>
    );
}
