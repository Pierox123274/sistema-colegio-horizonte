import type { LucideIcon } from 'lucide-react';
import {
    BookOpen,
    Building2,
    Calendar,
    ClipboardList,
    Eye,
    Flag,
    GraduationCap,
    Images,
    LayoutGrid,
    LogIn,
    Mail,
    MapPin,
    Palette,
    Sparkles,
    Target,
    Trophy,
    UserCircle,
    Users,
    Wrench,
} from 'lucide-react';

export type NavRouteItem = {
    label: string;
    description?: string;
    icon: LucideIcon;
    routeName: string;
    routeParams?: Record<string, string>;
};

export const nosotrosMenu: NavRouteItem[] = [
    {
        label: 'Presentación institucional',
        description: 'Quiénes somos y nuestra propuesta.',
        icon: Building2,
        routeName: 'public.nosotros',
    },
    {
        label: 'Historia',
        description: 'Tradición y legado del colegio.',
        icon: BookOpen,
        routeName: 'public.nosotros.historia',
    },
    {
        label: 'Misión y visión',
        description: 'Propósito y horizonte institucional.',
        icon: Eye,
        routeName: 'public.nosotros.mision-vision',
    },
    {
        label: 'Valores',
        description: 'Principios de convivencia.',
        icon: Flag,
        routeName: 'public.nosotros.valores',
    },
    {
        label: 'Infraestructura',
        description: 'Campus y espacios de aprendizaje.',
        icon: MapPin,
        routeName: 'public.nosotros.infraestructura',
    },
];

export const nivelesMenu = [
    {
        key: 'inicial',
        label: 'Inicial',
        grades: '3, 4 y 5 años',
        description: 'Juego, autonomía y bases sólidas.',
        color: 'from-amber-50 to-institutional-gold-soft/40 border-amber-200/60',
        icon: Sparkles,
        routeName: 'public.niveles.inicial',
    },
    {
        key: 'primaria',
        label: 'Primaria',
        grades: '1.º al 6.º',
        description: 'Lectoescritura, STEAM y hábitos.',
        color: 'from-sky-50 to-institutional-accent-soft/30 border-sky-200/50',
        icon: BookOpen,
        routeName: 'public.niveles.primaria',
    },
    {
        key: 'secundaria',
        label: 'Secundaria',
        grades: '1.º al 5.º',
        description: 'Pensamiento crítico y proyección.',
        color: 'from-institutional-surface-alt to-navy-50 border-navy-900/10',
        icon: GraduationCap,
        routeName: 'public.niveles.secundaria',
    },
] as const;

export const admisionMenu: NavRouteItem[] = [
    {
        label: 'Proceso de admisión',
        description: 'Pasos para tu familia.',
        icon: ClipboardList,
        routeName: 'public.admision',
    },
    {
        label: 'Requisitos',
        description: 'Documentación necesaria.',
        icon: Target,
        routeName: 'public.admision.requisitos',
    },
    {
        label: 'Matrícula 2026',
        description: 'Confirmación de vacante.',
        icon: Calendar,
        routeName: 'public.admision.matricula',
    },
    {
        label: 'Solicitar información',
        description: 'Agenda visita o escríbenos.',
        icon: Mail,
        routeName: 'public.contacto',
    },
];

export const vidaEscolarMenu: NavRouteItem[] = [
    {
        label: 'Actividades',
        description: 'Proyectos y experiencias.',
        icon: LayoutGrid,
        routeName: 'public.vida-escolar.actividades',
    },
    {
        label: 'Talleres',
        description: 'Clubes y formación extra.',
        icon: Wrench,
        routeName: 'public.vida-escolar.talleres',
    },
    {
        label: 'Deportes',
        description: 'Equipos y bienestar.',
        icon: Trophy,
        routeName: 'public.vida-escolar',
    },
    {
        label: 'Eventos',
        description: 'Ceremonias y celebraciones.',
        icon: Calendar,
        routeName: 'public.vida-escolar.eventos',
    },
    {
        label: 'Galería',
        description: 'Momentos del colegio.',
        icon: Images,
        routeName: 'public.galeria',
    },
];

export const portalMenu: NavRouteItem[] = [
    { label: 'Iniciar sesión', icon: LogIn, routeName: 'login' },
    { label: 'Portal estudiante', icon: UserCircle, routeName: 'login' },
    { label: 'Portal docente', icon: Users, routeName: 'login' },
    { label: 'Portal administrativo', icon: Building2, routeName: 'login' },
];

export const admissionTimeline = [
    { date: 'Mar', title: 'Puertas abiertas', desc: 'Recorrido por campus y charla informativa.' },
    { date: 'Abr', title: 'Entrevista familiar', desc: 'Conocemos expectativas del postulante.' },
    { date: 'May', title: 'Evaluación diagnóstica', desc: 'Adecuación al grado de ingreso.' },
    { date: 'Jun', title: 'Matrícula', desc: 'Confirmación de vacante y bienvenida.' },
];

export function navHref(item: { routeName: string; routeParams?: Record<string, string> }): string {
    return route(item.routeName, item.routeParams ?? {});
}
