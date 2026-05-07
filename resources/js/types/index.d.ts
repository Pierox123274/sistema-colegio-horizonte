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

/** Apoderados en detalle de estudiante (props dedicadas desde el backend). */
export type StudentGuardianLinkView = {
    id: number;
    full_name: string;
    relationship: string;
    phone: string;
    document_number: string | null;
    email: string | null;
    is_primary: boolean;
    is_financial_responsible: boolean;
    emergency_priority: number | null;
    observations: string | null;
};

/** Solo apoderados marcados como contacto principal (listado estudiantes). */
export type StudentPrimaryGuardianBrief = {
    id: number;
    first_name: string;
    last_name: string;
    phone: string;
};

export type StudentListRow = StudentSerializable & {
    guardians?: StudentPrimaryGuardianBrief[];
};

export type SelectOption = { value: string; label: string };

export type StudentFormCatalog = {
    document_types: SelectOption[];
    genders: SelectOption[];
    educational_levels: SelectOption[];
    statuses: SelectOption[];
    grades_by_level: Record<string, string[]>;
};

export type GuardianSerializable = {
    id: number;
    first_name: string;
    last_name: string;
    document_type: string;
    document_number: string | null;
    relationship_type: string;
    phone: string;
    secondary_phone: string | null;
    email: string | null;
    occupation: string | null;
    address: string | null;
    workplace: string | null;
    is_emergency_contact: boolean;
    created_at: string;
    updated_at: string;
    students_count?: number;
};

export type StudentOption = { value: number; label: string };

export type GuardianStudentLink = {
    student_id: number;
    relationship: string;
    is_primary: boolean;
    is_financial_responsible: boolean;
    emergency_priority: number | null;
    observations: string | null;
};

export type GuardianStudentLinkView = GuardianStudentLink & {
    student_code: string;
    student_name: string;
};

export type GuardianFormCatalog = {
    document_types: SelectOption[];
    relationship_types: SelectOption[];
};

export type GuardianCoreFields = {
    first_name: string;
    last_name: string;
    document_type: string;
    document_number: string;
    relationship_type: string;
    phone: string;
    secondary_phone: string;
    email: string;
    occupation: string;
    address: string;
    workplace: string;
    is_emergency_contact: boolean;
};

export type GuardianStudentLinkDraft = {
    student_id: number | '';
    relationship: string;
    is_primary: boolean;
    is_financial_responsible: boolean;
    emergency_priority: number | '';
    observations: string;
};

export type GuardianFullFormState = GuardianCoreFields & {
    students: GuardianStudentLinkDraft[];
};
