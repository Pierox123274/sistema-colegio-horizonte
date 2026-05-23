import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, Link } from '@inertiajs/react';
import { ExternalLink, ImagePlus, Newspaper, Upload } from 'lucide-react';

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
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <SectionTitle
                        title="Sitio web institucional"
                        description="Resumen del contenido público. Usa el menú lateral para cada módulo."
                    />
                    <div className="flex flex-wrap gap-2">
                        <a
                            href={route('public.home')}
                            target="_blank"
                            rel="noreferrer"
                            className="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-navy-900 shadow-sm hover:border-brand-yellow/60"
                        >
                            <ExternalLink className="h-4 w-4" />
                            Abrir web pública
                        </a>
                        <Link
                            href={route('intranet.cms.news.create')}
                            className="inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white hover:bg-navy-800"
                        >
                            <Newspaper className="h-4 w-4" />
                            Nueva noticia
                        </Link>
                        <Link
                            href={route('intranet.cms.media.index')}
                            className="inline-flex items-center gap-2 rounded-lg bg-brand-yellow px-4 py-2 text-sm font-semibold text-navy-950 hover:brightness-105"
                        >
                            <Upload className="h-4 w-4" />
                            Subir imagen
                        </Link>
                    </div>
                </div>

                <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    {[
                        { label: 'Páginas', value: stats.pages },
                        { label: 'Noticias', value: stats.news },
                        { label: 'Galerías', value: stats.galleries },
                        { label: 'Testimonios', value: stats.testimonials },
                        { label: 'Medios', value: media_count },
                    ].map((k) => (
                        <Card key={k.label} className="p-5">
                            <p className="text-2xl font-bold text-navy-900">{k.value}</p>
                            <p className="text-sm text-plomo">{k.label}</p>
                        </Card>
                    ))}
                </div>

                <div className="mt-10 grid gap-6 lg:grid-cols-2">
                    <Card className="p-6">
                        <h3 className="font-semibold text-navy-900">Contenido pendiente</h3>
                        <p className="mt-1 text-sm text-plomo">
                            Borradores y galerías inactivas por revisar.
                        </p>
                        {pendingTotal === 0 ? (
                            <p className="mt-4 text-sm text-emerald-700">
                                No hay pendientes destacados.
                            </p>
                        ) : (
                            <ul className="mt-4 space-y-2 text-sm">
                                {pending.draft_pages > 0 ? (
                                    <li>
                                        <Link
                                            href={route('intranet.cms.pages.index')}
                                            className="font-medium text-navy-800 hover:underline"
                                        >
                                            {pending.draft_pages} página(s) en borrador
                                        </Link>
                                    </li>
                                ) : null}
                                {pending.draft_news > 0 ? (
                                    <li>
                                        <Link
                                            href={route('intranet.cms.news.index')}
                                            className="font-medium text-navy-800 hover:underline"
                                        >
                                            {pending.draft_news} noticia(s) en borrador
                                        </Link>
                                    </li>
                                ) : null}
                                {pending.inactive_galleries > 0 ? (
                                    <li>
                                        <Link
                                            href={route('intranet.cms.galleries.index')}
                                            className="font-medium text-navy-800 hover:underline"
                                        >
                                            {pending.inactive_galleries} galería(s) inactiva(s)
                                        </Link>
                                    </li>
                                ) : null}
                            </ul>
                        )}
                    </Card>

                    <Card className="p-6">
                        <h3 className="font-semibold text-navy-900">Últimos cambios</h3>
                        <p className="mt-1 text-sm text-plomo">Auditoría del módulo CMS.</p>
                        <ul className="mt-4 max-h-64 space-y-3 overflow-y-auto text-sm">
                            {recent_changes.length === 0 ? (
                                <li className="text-plomo">Sin registros recientes.</li>
                            ) : (
                                recent_changes.map((log) => (
                                    <li
                                        key={log.id}
                                        className="border-b border-slate-100 pb-2 last:border-0"
                                    >
                                        <p className="font-medium text-navy-900">
                                            {log.description}
                                        </p>
                                        <p className="text-xs text-plomo">
                                            {log.user_name}
                                            {log.created_at
                                                ? ` · ${new Date(log.created_at).toLocaleString('es-PE')}`
                                                : ''}
                                        </p>
                                    </li>
                                ))
                            )}
                        </ul>
                    </Card>
                </div>

                <Card className="mt-6 flex items-center gap-4 p-5">
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
                        className="shrink-0 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                    >
                        Ir a medios
                    </Link>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
