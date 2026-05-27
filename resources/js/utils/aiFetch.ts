export async function postJson<T>(url: string, body: Record<string, unknown>): Promise<T> {
    const token =
        document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';

    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify(body),
    });

    if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        throw new Error((err as { message?: string }).message ?? 'Error en la solicitud IA');
    }

    return res.json() as Promise<T>;
}

export async function getJson<T>(url: string): Promise<T> {
    const res = await fetch(url, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
    });

    if (!res.ok) {
        throw new Error('Error al cargar datos IA');
    }

    return res.json() as Promise<T>;
}
