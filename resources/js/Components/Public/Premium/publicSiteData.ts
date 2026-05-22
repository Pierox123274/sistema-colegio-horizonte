import type { LucideIcon } from 'lucide-react';
import {
    BarChart3,
    BookOpen,
    Brain,
    ClipboardCheck,
    GraduationCap,
    LineChart,
    Shield,
    Sparkles,
    Users,
} from 'lucide-react';

export type NavItem = {
    label: string;
    href: string;
    isAnchor?: boolean;
};

export const premiumNavItems = (homeUrl: string): NavItem[] => [
    { label: 'Inicio', href: homeUrl },
    { label: 'Funcionalidades', href: `${homeUrl}#funcionalidades`, isAnchor: true },
    { label: 'IA educativa', href: `${homeUrl}#ia-educativa`, isAnchor: true },
    { label: 'LMS', href: `${homeUrl}#lms`, isAnchor: true },
    { label: 'Analítica', href: `${homeUrl}#analitica`, isAnchor: true },
    { label: 'Seguridad', href: `${homeUrl}#seguridad`, isAnchor: true },
    { label: 'Contacto', href: route('public.contacto') },
];

export const heroStats = [
    { value: 98, suffix: '%', label: 'Satisfacción docente' },
    { value: 24, suffix: '/7', label: 'Plataforma activa' },
    { value: 12, suffix: '+', label: 'Módulos integrados' },
    { value: 3, suffix: '', label: 'Niveles educativos' },
];

export type FeatureModule = {
    id: string;
    badge: string;
    title: string;
    description: string;
    icon: LucideIcon;
    highlights: string[];
    accent: 'navy' | 'violet' | 'amber' | 'emerald' | 'sky';
};

export const featureModules: FeatureModule[] = [
    {
        id: 'erp',
        badge: 'ERP Académico',
        title: 'Gestión integral del colegio',
        description:
            'Matrícula, asistencia, notas, finanzas y reportes en un solo ecosistema con trazabilidad ISO.',
        icon: GraduationCap,
        highlights: ['Asistencia en tiempo real', 'Registro de notas', 'Matrícula digital', 'Caja y pensiones', 'Reportes PDF/CSV'],
        accent: 'navy',
    },
    {
        id: 'lms',
        badge: 'Aula virtual LMS',
        title: 'Aprendizaje digital de clase mundial',
        description:
            'Tareas, evaluaciones online, calendario académico y recursos multimedia por curso y sección.',
        icon: BookOpen,
        highlights: ['Tareas y entregas', 'Exámenes online', 'Calendario', 'Progreso visual', 'Recursos educativos'],
        accent: 'violet',
    },
    {
        id: 'ia-educativa',
        badge: 'IA Educativa',
        title: 'Inteligencia que acompaña',
        description:
            'Tutor IA, insights docentes, riesgo académico y recomendaciones personalizadas con auditoría.',
        icon: Sparkles,
        highlights: ['Tutor conversacional', 'Insights docente', 'Riesgo académico', 'Recomendaciones', 'Panel institucional'],
        accent: 'amber',
    },
    {
        id: 'diagnostico',
        badge: 'Diagnóstico inteligente',
        title: 'Nivelación y rutas adaptativas',
        description:
            'Diagnósticos adaptativos, banco de preguntas y rutas de aprendizaje según competencias.',
        icon: Brain,
        highlights: ['Niveles automáticos', 'Rutas de aprendizaje', 'Banco de preguntas', 'Progreso por competencia', 'Panel pedagógico'],
        accent: 'emerald',
    },
    {
        id: 'analitica',
        badge: 'Analítica institucional',
        title: 'Decisiones con datos',
        description:
            'Dashboards ejecutivos, KPI académicos y financieros, exportación y métricas por sección.',
        icon: BarChart3,
        highlights: ['KPI en vivo', 'Gráficos Recharts', 'Métricas por rol', 'Export PDF/CSV', 'Vista docente'],
        accent: 'sky',
    },
    {
        id: 'seguridad',
        badge: 'Seguridad y auditoría',
        title: 'Confianza enterprise',
        description:
            'Roles Spatie, auditoría ISO, sesiones activas y monitoreo de accesos para cumplimiento institucional.',
        icon: Shield,
        highlights: ['Auditoría completa', 'Roles granulares', 'Sesiones seguras', 'Políticas Laravel', 'Trazabilidad'],
        accent: 'navy',
    },
];

export const demoPanels = [
    { title: 'Portal docente', desc: 'Asistencia, notas, aula virtual y riesgo académico.', icon: Users },
    { title: 'Portal estudiante', desc: 'Tareas, diagnósticos, tutor IA y calendario.', icon: ClipboardCheck },
    { title: 'LMS institucional', desc: 'Aulas virtuales, evaluaciones y entregas.', icon: BookOpen },
    { title: 'Analítica ejecutiva', desc: 'KPI, tendencias y reportes institucionales.', icon: LineChart },
];

export const testimonials = [
    {
        quote: 'La plataforma unificó matrícula, notas y comunicados. Nuestro equipo docente ahorra horas cada semana.',
        name: 'Mg. Patricia Rojas',
        role: 'Directora académica',
        org: 'I.E.P. Horizonte',
    },
    {
        quote: 'El aula virtual y las evaluaciones online elevaron el engagement. Los estudiantes siguen su progreso en tiempo real.',
        name: 'Prof. Luis Mendoza',
        role: 'Coordinador de Secundaria',
        org: 'Área de Ciencias',
    },
    {
        quote: 'Como apoderado, valoramos la transparencia: pagos, asistencia y calificaciones en un solo acceso seguro.',
        name: 'Sra. Carmen Vela',
        role: 'Apoderada',
        org: 'Comunidad educativa',
    },
];

export const techBadges = ['Laravel 12', 'React + TS', 'Inertia', 'IA adaptativa', 'ISO 27001', 'BDD + Cypress'];
