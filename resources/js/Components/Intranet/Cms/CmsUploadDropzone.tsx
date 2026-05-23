import { Upload } from 'lucide-react';
import { DragEvent, useRef, useState } from 'react';

type Props = {
    onFiles: (files: FileList) => void;
    accept?: string;
    disabled?: boolean;
    label?: string;
};

export function CmsUploadDropzone({
    onFiles,
    accept = 'image/jpeg,image/png,image/webp,image/svg+xml',
    disabled = false,
    label = 'Arrastra imágenes aquí o haz clic para subir',
}: Props) {
    const inputRef = useRef<HTMLInputElement>(null);
    const [dragOver, setDragOver] = useState(false);

    const handleFiles = (list: FileList | null) => {
        if (!list?.length || disabled) {
            return;
        }
        onFiles(list);
    };

    const onDrop = (e: DragEvent) => {
        e.preventDefault();
        setDragOver(false);
        handleFiles(e.dataTransfer.files);
    };

    return (
        <div
            role="button"
            tabIndex={0}
            onKeyDown={(e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    inputRef.current?.click();
                }
            }}
            onClick={() => !disabled && inputRef.current?.click()}
            onDragOver={(e) => {
                e.preventDefault();
                if (!disabled) {
                    setDragOver(true);
                }
            }}
            onDragLeave={() => setDragOver(false)}
            onDrop={onDrop}
            className={`flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed px-6 py-10 transition ${
                dragOver
                    ? 'border-brand-yellow bg-amber-50/80'
                    : 'border-slate-300 bg-slate-50/80 hover:border-navy-400 hover:bg-white'
            } ${disabled ? 'pointer-events-none opacity-50' : ''}`}
        >
            <input
                ref={inputRef}
                type="file"
                accept={accept}
                multiple
                className="sr-only"
                disabled={disabled}
                onChange={(e) => handleFiles(e.target.files)}
            />
            <span className="flex h-12 w-12 items-center justify-center rounded-full bg-navy-900/5">
                <Upload className="h-6 w-6 text-navy-800" />
            </span>
            <p className="mt-3 text-center text-sm font-medium text-navy-900">{label}</p>
            <p className="mt-1 text-center text-xs text-plomo">JPG, PNG, WebP o SVG · máx. 5 MB</p>
        </div>
    );
}
