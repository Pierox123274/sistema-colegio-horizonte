/**
 * Imágenes institucionales (demo).
 * Prioridad: archivo en /images/public/{name} si existe; si no, URL de respaldo.
 */

export type PublicImageKey =
    | 'hero'
    | 'heroInicial'
    | 'heroPrimaria'
    | 'heroSecundaria'
    | 'heroAdmision'
    | 'vidaTalleres'
    | 'vidaDeportes'
    | 'vidaArte'
    | 'vidaCiencia'
    | 'vidaLiderazgo'
    | 'vidaEventos'
    | 'newsAdmision'
    | 'newsFeria'
    | 'newsOlimpiadas'
    | 'galeria01'
    | 'galeria02'
    | 'galeria03'
    | 'galeria04'
    | 'galeria05'
    | 'galeria06'
    | 'galeria07'
    | 'galeria08'
    | 'galeria09'
    | 'galeria10'
    | 'galeria11'
    | 'galeria12';

const fallback: Record<PublicImageKey, string> = {
    hero: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1600&q=80&auto=format',
    heroInicial: 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=1400&q=80&auto=format',
    heroPrimaria: 'https://images.unsplash.com/photo-1580582932707-520ed925b3d4?w=1400&q=80&auto=format',
    heroSecundaria: 'https://images.unsplash.com/photo-1427504494785-3a9ca7044de0?w=1400&q=80&auto=format',
    heroAdmision: 'https://images.unsplash.com/photo-1541339907198-e08756dedf03?w=1400&q=80&auto=format',
    vidaTalleres: 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=900&q=80&auto=format',
    vidaDeportes: 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=900&q=80&auto=format',
    vidaArte: 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=900&q=80&auto=format',
    vidaCiencia: 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=900&q=80&auto=format',
    vidaLiderazgo: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=900&q=80&auto=format',
    vidaEventos: 'https://images.unsplash.com/photo-1523580494863-6f3031224c94?w=900&q=80&auto=format',
    newsAdmision: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=1200&q=80&auto=format',
    newsFeria: 'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=1200&q=80&auto=format',
    newsOlimpiadas: 'https://images.unsplash.com/photo-1434030214721-40b671f05c15?w=1200&q=80&auto=format',
    galeria01: 'https://images.unsplash.com/photo-1562774053-701939374585?w=800&q=80&auto=format',
    galeria02: 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?w=800&q=80&auto=format',
    galeria03: 'https://images.unsplash.com/photo-1577896851231-70ef94aedd3e?w=800&q=80&auto=format',
    galeria04: 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&q=80&auto=format',
    galeria05: 'https://images.unsplash.com/photo-1517245386807-bb43f82c5c68?w=800&q=80&auto=format',
    galeria06: 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800&q=80&auto=format',
    galeria07: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800&q=80&auto=format',
    galeria08: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&q=80&auto=format',
    galeria09: 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=800&q=80&auto=format',
    galeria10: 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&q=80&auto=format',
    galeria11: 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=800&q=80&auto=format',
    galeria12: 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=800&q=80&auto=format',
};

const localPath: Partial<Record<PublicImageKey, string>> = {
    hero: '/images/public/hero-campus.jpg',
    heroInicial: '/images/public/hero-inicial.jpg',
    heroPrimaria: '/images/public/hero-primaria.jpg',
    heroSecundaria: '/images/public/hero-secundaria.jpg',
    heroAdmision: '/images/public/hero-admision.jpg',
};

export function publicImage(key: PublicImageKey): string {
    return localPath[key] ?? fallback[key];
}

export const levelHeroImage: Record<'inicial' | 'primaria' | 'secundaria', PublicImageKey> = {
    inicial: 'heroInicial',
    primaria: 'heroPrimaria',
    secundaria: 'heroSecundaria',
};
