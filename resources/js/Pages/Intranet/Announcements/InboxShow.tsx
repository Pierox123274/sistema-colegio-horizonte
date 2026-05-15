import PortalAnnouncementShow from '@/Components/Announcements/PortalAnnouncementShow';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type Props = PageProps<{
    announcement: Parameters<typeof PortalAnnouncementShow>[0]['announcement'];
    back_href: string;
}>;

export default function AnnouncementsInboxShow() {
    const { announcement, back_href } = usePage<Props>().props;

    return (
        <IntranetLayout title="Comunicado">
            <Head title={announcement.title} />
            <PortalAnnouncementShow announcement={announcement} back_href={back_href} />
        </IntranetLayout>
    );
}
