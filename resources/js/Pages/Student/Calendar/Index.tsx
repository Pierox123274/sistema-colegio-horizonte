import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type Event = { id: number; title: string; event_type: string; starts_at: string };

type Props = PageProps<{ events: Event[] }>;

export default function StudentCalendarIndex() {
    const { events } = usePage<Props>().props;

    return (
        <StudentLayout title="Calendario">
            <Head title="Calendario" />
            <PageContainer>
                <SectionTitle title="Mi calendario" description="Próximas tareas y evaluaciones." />
                <Card>
                    <ul className="divide-y divide-slate-100">
                        {events.map((e) => (
                            <li key={e.id} className="py-3 text-sm">
                                <span className="font-medium">{e.title}</span>
                                <span className="ml-2 text-plomo">
                                    {new Date(e.starts_at).toLocaleDateString()}
                                </span>
                            </li>
                        ))}
                    </ul>
                </Card>
            </PageContainer>
        </StudentLayout>
    );
}
