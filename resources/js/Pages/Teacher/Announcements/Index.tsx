import PortalAnnouncementsIndex from '@/Components/Announcements/PortalAnnouncementsIndex';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { AnnouncementCardPayload, PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type Props = PageProps<{
    announcements: { data: AnnouncementCardPayload[]; links: { url: string | null; label: string; active: boolean }[] };
    filters: { search: string; priority: string; unread_only: string };
    unread_count: number;
    catalog: { priorities: { value: string; label: string }[] };
}>;

export default function TeacherAnnouncementsIndex() {
    const props = usePage<Props>().props;

    return (
        <TeacherLayout title="Comunicados">
            <Head title="Comunicados" />
            <PortalAnnouncementsIndex title="Comunicados" {...props} />
        </TeacherLayout>
    );
}
