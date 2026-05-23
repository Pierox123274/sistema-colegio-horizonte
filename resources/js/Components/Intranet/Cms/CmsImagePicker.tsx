import { CmsImagePreview } from '@/Components/Intranet/Cms/CmsImagePreview';
import { CmsMediaLibrary } from '@/Components/Intranet/Cms/CmsMediaLibrary';
import { cmsStorageUrl } from '@/lib/cmsMedia';
import { ImagePlus } from 'lucide-react';
import { useState } from 'react';

type Props = {
    label: string;
    value: string;
    onChange: (path: string) => void;
    hint?: string;
};

export function CmsImagePicker({ label, value, onChange, hint }: Props) {
    const [libraryOpen, setLibraryOpen] = useState(false);

    return (
        <div className="space-y-2">
            <div className="flex items-center justify-between gap-2">
                <label className="text-sm font-medium text-navy-900">{label}</label>
                {value ? (
                    <span className="truncate text-xs text-plomo font-mono max-w-[12rem]">
                        {value}
                    </span>
                ) : null}
            </div>
            <CmsImagePreview path={value} onClear={value ? () => onChange('') : undefined} />
            <div className="flex flex-wrap gap-2">
                <button
                    type="button"
                    onClick={() => setLibraryOpen(true)}
                    className="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-navy-900 shadow-sm transition hover:border-navy-400"
                >
                    <ImagePlus className="h-4 w-4" />
                    Elegir imagen
                </button>
                {value && cmsStorageUrl(value) ? (
                    <a
                        href={cmsStorageUrl(value) ?? '#'}
                        target="_blank"
                        rel="noreferrer"
                        className="rounded-lg px-3 py-2 text-sm font-medium text-navy-800 hover:underline"
                    >
                        Ver en tamaño completo
                    </a>
                ) : null}
            </div>
            {hint ? <p className="text-xs text-plomo">{hint}</p> : null}
            <CmsMediaLibrary
                open={libraryOpen}
                onClose={() => setLibraryOpen(false)}
                onSelect={(item) => onChange(item.path)}
            />
        </div>
    );
}
