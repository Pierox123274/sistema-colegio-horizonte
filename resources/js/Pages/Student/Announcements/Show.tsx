import PortalAnnouncementShow from '@/Components/Announcements/PortalAnnouncementShow';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type Props = PageProps<{
    announcement: Parameters<typeof PortalAnnouncementShow>[0]['announcement'];
    back_href: string;
}>;

export default function StudentAnnouncementShow() {
    const { announcement, back_href } = usePage<Props>().props;

    return (
        <StudentLayout title="Comunicado">
            <Head title={announcement.title} />
            <PortalAnnouncementShow announcement={announcement} back_href={back_href} />
        </StudentLayout>
    );
}
