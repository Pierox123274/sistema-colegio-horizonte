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
    active_routes?: string[];
    activeRoutes?: string[];
    children?: SidebarNavItem[];
}

export type FlashMessages = {
    success?: string | null;
    error?: string | null;
    ai?: {
        ai_reply?: string | null;
        ai_meta?: {
            success?: boolean;
            cached?: boolean;
            model?: string;
            fallback?: boolean;
        } | null;
    };
};

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User | null;
    };
    sidebarNav: SidebarNavItem[];
    /** URL del logotipo en sidebar intranet (docente sin admin → portal docente). */
    intranetHomeHref?: string;
    /** Menú del portal docente; vacío si el rol no aplica. */
    teacherNav?: SidebarNavItem[];
    /** Menú del portal estudiante; vacío si el rol no aplica. */
    studentNav?: SidebarNavItem[];
    /** Campana de comunicados (no leídos + recientes). */
    announcementBell?: {
        unread_count: number;
        recent: AnnouncementCardPayload[];
        index_href: string;
    } | null;
    notificationCenter?: {
        unread_count: number;
        recent: NotificationPayload[];
        center_href: string;
    } | null;
    current_route: string | null;
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

/** Catálogo de formulario de matrícula (EnrollmentFormCatalog::build). */
export type EnrollmentFormCatalog = {
    academic_years: SelectOption[];
    levels: SelectOption[];
    grades_by_level: Record<string, SelectOption[]>;
    sections_by_grade: Record<string, SelectOption[]>;
    classrooms_by_section: Record<string, SelectOption[]>;
    classrooms_without_section: SelectOption[];
    statuses: SelectOption[];
};

/** Resultado de búsqueda de estudiante (JSON). */
export type EnrollmentStudentSearchHit = {
    id: number;
    code: string;
    first_name: string;
    last_name: string;
    document_number: string | null;
};

/** Vista previa con apoderados para el formulario de matrícula. */
export type EnrollmentStudentPreview = {
    id: number;
    code: string;
    first_name: string;
    last_name: string;
    document_number: string | null;
    document_type: string | null;
    guardians: SelectOption[];
};

export type EnrollmentFormState = {
    enrollment_code: string;
    student_id: string;
    guardian_id: string;
    academic_year_id: string;
    educational_level_id: string;
    grade_id: string;
    section_id: string;
    classroom_id: string;
    enrollment_date: string;
    amount: string;
    status: string;
    observations: string;
};

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

export type AnnouncementCardPayload = {
    id: number;
    title: string;
    content_excerpt: string;
    priority: string;
    priority_label: string;
    audience_type: string;
    audience_label: string;
    starts_at: string;
    starts_at_label: string;
    ends_at: string | null;
    ends_at_label: string | null;
    has_attachment: boolean;
    is_read: boolean;
    is_active: boolean;
    created_by: { id: number; name: string } | null;
    show_href: string;
};

export type NotificationPayload = {
    id: string;
    title: string;
    message: string;
    category: string;
    priority: string;
    action_url: string | null;
    action_label: string | null;
    read_at: string | null;
    is_read: boolean;
    created_at: string | null;
    created_at_label: string | null;
};
