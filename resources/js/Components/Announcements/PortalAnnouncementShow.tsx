import AnnouncementPriorityBadge from '@/Components/Announcements/AnnouncementPriorityBadge';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { Link } from '@inertiajs/react';

type Detail = {
    title: string;
    content: string;
    priority: string;
    priority_label: string;
    starts_at_label: string;
    ends_at_label: string | null;
    has_attachment: boolean;
    attachment_url: string | null;
    attachment_mime: string | null;
    attachment_original_name: string | null;
    is_image_attachment: boolean;
    is_pdf_attachment: boolean;
    created_by: { name: string } | null;
};

export default function PortalAnnouncementShow({
    announcement,
    back_href,
}: {
    announcement: Detail;
    back_href: string;
}) {
    return (
        <PageContainer>
            <SectionTitle
                title={announcement.title}
                description={`Publicado ${announcement.starts_at_label}${
                    announcement.ends_at_label ? ` · Vence ${announcement.ends_at_label}` : ''
                }`}
                actions={
                    <Link
                        href={back_href}
                        className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold"
                    >
                        Volver
                    </Link>
                }
            />

            <Card>
                <div className="mb-4 flex flex-wrap items-center gap-2">
                    <AnnouncementPriorityBadge
                        priority={announcement.priority}
                        label={announcement.priority_label}
                    />
                    {announcement.created_by && (
                        <span className="text-sm text-plomo">Por {announcement.created_by.name}</span>
                    )}
                </div>
                <div
                    className="prose prose-sm max-w-none text-navy-900"
                    dangerouslySetInnerHTML={{ __html: announcement.content.replace(/\n/g, '<br />') }}
                />
                {announcement.has_attachment && announcement.attachment_url && (
                    <div className="mt-6 border-t border-plomo/10 pt-4">
                        <p className="mb-2 text-sm font-semibold text-navy-900">Adjunto</p>
                        {announcement.is_image_attachment ? (
                            <img
                                src={announcement.attachment_url}
                                alt={announcement.attachment_original_name ?? 'Adjunto'}
                                className="max-h-96 rounded-lg border border-plomo/15"
                            />
                        ) : (
                            <a
                                href={announcement.attachment_url}
                                target="_blank"
                                rel="noreferrer"
                                className="text-sm font-semibold text-navy-900 underline"
                            >
                                {announcement.attachment_original_name ?? 'Descargar adjunto'}
                            </a>
                        )}
                    </div>
                )}
            </Card>
        </PageContainer>
    );
}
