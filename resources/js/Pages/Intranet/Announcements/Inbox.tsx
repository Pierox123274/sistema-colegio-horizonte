import PortalAnnouncementsIndex from '@/Components/Announcements/PortalAnnouncementsIndex';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { AnnouncementCardPayload, PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type Props = PageProps<{
    announcements: { data: AnnouncementCardPayload[]; links: { url: string | null; label: string; active: boolean }[] };
    filters: { search: string; priority: string; unread_only: string };
    unread_count: number;
    catalog: { priorities: { value: string; label: string }[] };
}>;

export default function AnnouncementsInbox() {
    const props = usePage<Props>().props;

    return (
        <IntranetLayout title="Comunicados">
            <Head title="Comunicados" />
            <PortalAnnouncementsIndex title="Mis comunicados" {...props} />
        </IntranetLayout>
    );
}
