export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string | null;
    roles: string[];
}

export interface SidebarNavItem {
    label: string;
    href: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User | null;
    };
    sidebarNav: SidebarNavItem[];
};
