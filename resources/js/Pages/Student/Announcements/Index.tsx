import PortalAnnouncementsIndex from '@/Components/Announcements/PortalAnnouncementsIndex';
import StudentLayout from '@/Layouts/StudentLayout';
import type { AnnouncementCardPayload, PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type Props = PageProps<{
    announcements: { data: AnnouncementCardPayload[]; links: { url: string | null; label: string; active: boolean }[] };
    filters: { search: string; priority: string; unread_only: string };
    unread_count: number;
    catalog: { priorities: { value: string; label: string }[] };
}>;

export default function StudentAnnouncementsIndex() {
    const props = usePage<Props>().props;

    return (
        <StudentLayout title="Comunicados">
            <Head title="Comunicados" />
            <PortalAnnouncementsIndex title="Comunicados" {...props} />
        </StudentLayout>
    );
}
