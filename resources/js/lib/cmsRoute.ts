/** Resuelve rutas Ziggy del CMS sin romper si el nombre no existe. */
export function cmsRoute(routeName?: string, fallback = '#'): string {
    if (!routeName) {
        return fallback;
    }

    try {
        return route(routeName as never);
    } catch {
        return fallback;
    }
}
