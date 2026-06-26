export type HealthStatus = 'ok' | 'warning' | 'critical';

export function healthStatusTone(status: HealthStatus): string {
    if (status === 'critical') {
        return 'border-red-200 bg-red-50 text-red-800';
    }
    if (status === 'warning') {
        return 'border-amber-200 bg-amber-50 text-amber-900';
    }
    return 'border-emerald-200 bg-emerald-50 text-emerald-900';
}

export function computeDiskFreePercent(
    freeBytes: number | null | undefined,
    totalBytes: number | null | undefined,
): number | null {
    if (freeBytes == null || totalBytes == null || totalBytes <= 0) {
        return null;
    }

    return Math.round((freeBytes / totalBytes) * 100);
}

export function checkStatusBadgeClass(status: HealthStatus): string {
    if (status === 'critical') {
        return 'bg-red-100 text-red-700';
    }
    if (status === 'warning') {
        return 'bg-amber-100 text-amber-700';
    }
    return 'bg-emerald-100 text-emerald-700';
}
