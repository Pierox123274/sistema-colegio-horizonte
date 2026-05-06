export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string | null;
    roles: string[];
}

export interface SidebarNavItem {
    label: string;
    href: string | null;
    icon: string;
    disabled: boolean;
}

export type FlashMessages = {
    success?: string | null;
    error?: string | null;
};

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User | null;
    };
    sidebarNav: SidebarNavItem[];
    flash?: FlashMessages;
    /** Web pública (PublicSiteController) */
    canLogin?: boolean;
    canRegister?: boolean;
};

/** Props serializados del modelo estudiante (Inertia). */
export type StudentSerializable = {
    id: number;
    code: string;
    first_name: string;
    last_name: string;
    document_type: string;
    document_number: string | null;
    birth_date: string;
    gender: string;
    educational_level: string;
    grade: string;
    section: string | null;
    status: string;
    address: string | null;
    phone: string | null;
    email: string | null;
    medical_observations: string | null;
    created_at: string;
    updated_at: string;
};

export type SelectOption = { value: string; label: string };

export type StudentFormCatalog = {
    document_types: SelectOption[];
    genders: SelectOption[];
    educational_levels: SelectOption[];
    statuses: SelectOption[];
    grades_by_level: Record<string, string[]>;
};
