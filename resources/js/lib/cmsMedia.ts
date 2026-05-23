export type CmsMediaItem = {
    id: number;
    path: string;
    url: string | null;
    filename: string;
    mime: string | null;
    size: number;
    size_label: string;
    alt: string | null;
    is_image: boolean;
    created_at?: string | null;
};

/** URL pública para rutas CMS, storage o assets estáticos. */
export function cmsStorageUrl(path: string | null | undefined): string | null {
    if (!path) {
        return null;
    }
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('//')) {
        return path;
    }
    if (path.startsWith('storage/')) {
        return `/${path}`;
    }
    if (path.startsWith('/')) {
        return path;
    }
    if (path.startsWith('images/')) {
        return `/${path}`;
    }

    return `/storage/${path}`;
}

export function formatBytes(bytes: number): string {
    if (bytes < 1024) {
        return `${bytes} B`;
    }
    if (bytes < 1024 * 1024) {
        return `${(bytes / 1024).toFixed(1)} KB`;
    }
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

export async function copyToClipboard(text: string): Promise<boolean> {
    try {
        await navigator.clipboard.writeText(text);
        return true;
    } catch {
        return false;
    }
}
