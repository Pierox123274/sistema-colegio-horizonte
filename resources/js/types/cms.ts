export type CmsSeo = {
    title: string;
    description: string;
    image?: string | null;
    robotsIndex?: boolean;
};

export type CmsSettings = {
    schoolName: string;
    schoolTagline: string;
    contact: {
        phone?: string;
        email?: string;
        address?: string;
        hours?: string;
    };
    social: Record<string, string>;
    logoUrl?: string | null;
    faviconUrl?: string | null;
};

export type CmsHero = {
    badge?: string | null;
    title: string;
    subtitle?: string | null;
    image?: string | null;
    primaryCta?: { label: string; href: string } | null;
    secondaryCta?: { label: string; href: string } | null;
};

export type CmsSectionPayload = Record<string, unknown>;

export type CmsSection = {
    key: string;
    title?: string | null;
    payload: CmsSectionPayload;
};

export type CmsNewsCard = {
    slug: string;
    title: string;
    excerpt: string;
    date: string;
    category: string;
    image: string;
    featured?: boolean;
};

export type CmsTestimonial = {
    name: string;
    role: string;
    org?: string | null;
    quote: string;
    photo?: string | null;
};

export type CmsGalleryItem = {
    id: string;
    title: string;
    category: string;
    image: string;
    span?: string;
};

export type CmsPageBrief = {
    slug: string;
    title: string;
    subtitle?: string | null;
    body?: string | null;
    template?: string | null;
};

export type CmsMenuItem = {
    label: string;
    href: string;
    target?: string;
    children?: CmsMenuItem[];
};

export type CmsMenu = {
    name: string;
    items: CmsMenuItem[];
};
