import { cmsStorageUrl } from '@/lib/cmsMedia';
import { ImageOff, X } from 'lucide-react';

type Props = {
    path: string | null | undefined;
    alt?: string;
    onClear?: () => void;
    className?: string;
};

export function CmsImagePreview({ path, alt = '', onClear, className = '' }: Props) {
    const url = cmsStorageUrl(path);

    if (!url) {
        return (
            <div
                className={`flex aspect-video w-full items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-slate-400 ${className}`}
            >
                <div className="text-center">
                    <ImageOff className="mx-auto h-8 w-8 opacity-50" />
                    <p className="mt-2 text-xs">Sin imagen seleccionada</p>
                </div>
            </div>
        );
    }

    return (
        <div className={`relative overflow-hidden rounded-xl border border-slate-200 ${className}`}>
            <img src={url} alt={alt} className="aspect-video w-full object-cover" />
            {onClear ? (
                <button
                    type="button"
                    onClick={onClear}
                    className="absolute right-2 top-2 rounded-full bg-navy-900/80 p-1.5 text-white transition hover:bg-navy-900"
                    title="Quitar imagen"
                >
                    <X className="h-4 w-4" />
                </button>
            ) : null}
        </div>
    );
}
