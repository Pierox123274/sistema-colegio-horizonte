import { AppBadge } from '@/Components/App/AppBadge';
import { AppCard } from '@/Components/App/AppCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { AppSection } from '@/Components/App/AppSection';
import { AppStatCard } from '@/Components/App/AppStatCard';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, Link } from '@inertiajs/react';
import {
    ExternalLink,
    FileText,
    Image,
    ImagePlus,
    MessageSquareQuote,
    Newspaper,
    SquareStack,
    Upload,
} from 'lucide-react';

type Overview = {
    stats: { pages: number; news: number; galleries: number; testimonials: number };
    media_count: number;
    pending: {
        draft_pages: number;
        draft_news: number;
        inactive_galleries: number;
    };
    recent_changes: {
        id: number;
        description: string;
        user_name: string;
        created_at: string | null;
    }[];
};

type Props = { overview: Overview };

export default function CmsDashboard({ overview }: Props) {
    const { stats, pending, recent_changes, media_count } = overview;
    const pendingTotal =
        pending.draft_pages + pending.draft_news + pending.inactive_galleries;

    return (
        <IntranetLayout>
            <Head title="CMS — Resumen" />
            <PageContainer>
                <AppPageHeader
                    title="Sitio web institucional"
                    description="Resumen del contenido público. Usa el menú lateral para cada módulo."
                    actions={
                        <div className="flex flex-wrap gap-2">
                        <a
                            href={route('public.home')}
                            target="_blank"
                            rel="noreferrer"
                            className="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-navy-900 shadow-sm app-transition hover:border-brand-yellow/60"
                        >
                            <ExternalLink className="h-4 w-4" />
                            Abrir web pública
                        </a>
                        <Link
                            href={route('intranet.cms.news.create')}
                            className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white app-transition hover:bg-navy-800"
                        >
                            <Newspaper className="h-4 w-4" />
                            Nueva noticia
                        </Link>
                        <Link
                            href={route('intranet.cms.media.index')}
                            className="inline-flex items-center gap-2 rounded-lg bg-brand-yellow px-4 py-2 text-sm font-semibold text-navy-950 app-transition hover:brightness-105"
                        >
                            <Upload className="h-4 w-4" />
                            Subir imagen
                        </Link>
                    </div>
                    }
                />

                <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    {[
                        { label: 'Páginas', value: stats.pages, icon: FileText },
                        { label: 'Noticias', value: stats.news, icon: Newspaper },
                        { label: 'Galerías', value: stats.galleries, icon: Image },
                        { label: 'Testimonios', value: stats.testimonials, icon: MessageSquareQuote },
                        { label: 'Medios', value: media_count, icon: SquareStack },
                    ].map((k) => (
                        <AppStatCard
                            key={k.label}
                            title={k.label}
                            value={String(k.value)}
                            icon={k.icon}
                        />
                    ))}
                </div>

                <div className="mt-10 grid gap-6 lg:grid-cols-2">
                    <AppSection title="Contenido pendiente" description="Borradores y galerías inactivas por revisar.">
                        <div className="mt-2">
                        {pendingTotal === 0 ? (
                            <p className="text-sm text-emerald-700 dark:text-emerald-300">
                                No hay pendientes destacados.
                            </p>
                        ) : (
                            <ul className="mt-4 space-y-2 text-sm">
                                {pending.draft_pages > 0 ? (
                                    <li>
                                        <Link
                                            href={route('intranet.cms.pages.index')}
                                            className="font-medium text-navy-800 app-transition hover:underline dark:text-slate-200"
                                        >
                                            {pending.draft_pages} página(s) en borrador
                                        </Link>
                                    </li>
                                ) : null}
                                {pending.draft_news > 0 ? (
                                    <li>
                                        <Link
                                            href={route('intranet.cms.news.index')}
                                            className="font-medium text-navy-800 app-transition hover:underline dark:text-slate-200"
                                        >
                                            {pending.draft_news} noticia(s) en borrador
                                        </Link>
                                    </li>
                                ) : null}
                                {pending.inactive_galleries > 0 ? (
                                    <li>
                                        <Link
                                            href={route('intranet.cms.galleries.index')}
                                            className="font-medium text-navy-800 app-transition hover:underline dark:text-slate-200"
                                        >
                                            {pending.inactive_galleries} galería(s) inactiva(s)
                                        </Link>
                                    </li>
                                ) : null}
                            </ul>
                        )}
                        </div>
                    </AppSection>

                    <AppSection title="Últimos cambios" description="Auditoría del módulo CMS.">
                        <ul className="mt-4 max-h-64 space-y-3 overflow-y-auto text-sm">
                            {recent_changes.length === 0 ? (
                                <li className="text-plomo">Sin registros recientes.</li>
                            ) : (
                                recent_changes.map((log) => (
                                    <li
                                        key={log.id}
                                        className="border-b border-slate-100 pb-2 last:border-0 dark:border-white/10"
                                    >
                                        <p className="font-medium text-navy-900">
                                            {log.description}
                                        </p>
                                        <p className="text-xs text-plomo">
                                            {log.user_name}
                                            <AppBadge tone="info">CMS</AppBadge>
                                            {log.created_at
                                                ? ` · ${new Date(log.created_at).toLocaleString('es-PE')}`
                                                : ''}
                                        </p>
                                    </li>
                                ))
                            )}
                        </ul>
                    </AppSection>
                </div>

                <AppCard className="mt-6" contentClassName="flex items-center gap-4 p-5">
                    <ImagePlus className="h-8 w-8 text-navy-700" />
                    <div className="flex-1">
                        <p className="font-semibold text-navy-900">Gestión de imágenes</p>
                        <p className="text-sm text-plomo">
                            Sube y reutiliza imágenes en hero, noticias, páginas y galerías desde la
                            biblioteca de medios.
                        </p>
                    </div>
                    <Link
                        href={route('intranet.cms.media.index')}
                        className="shrink-0 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white app-transition hover:bg-navy-800"
                    >
                        Ir a medios
                    </Link>
                </AppCard>
            </PageContainer>
        </IntranetLayout>
    );
}
