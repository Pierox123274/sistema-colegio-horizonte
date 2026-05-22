export function scrollToSection(hash: string, offset = 88): void {
    const id = hash.replace(/^#/, '');
    const el = document.getElementById(id);
    if (!el) {
        if (hash.startsWith('#')) {
            window.location.href = `${route('public.home')}${hash}`;
        }
        return;
    }
    const top = el.getBoundingClientRect().top + window.scrollY - offset;
    window.scrollTo({ top, behavior: 'smooth' });
}

export function homeSectionHref(sectionId: string): string {
    const onHome = route().current('public.home');
    return onHome ? `#${sectionId}` : `${route('public.home')}#${sectionId}`;
}
